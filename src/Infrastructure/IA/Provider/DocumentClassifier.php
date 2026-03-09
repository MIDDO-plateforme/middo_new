<?php

namespace App\Infrastructure\IA\Provider;

class DocumentClassifier
{
    /**
     * Retourne un tableau :
     * [
     *   'domain' => 'admin|business|association|legal|comptable|autre',
     *   'type'   => 'facture|devis|statuts|contrat|attestation|...'
     * ]
     */
    public function classify(?string $text): array
    {
        $text = mb_strtolower($text ?? '');

        // Par défaut
        $result = [
            'domain' => 'autre',
            'type' => 'inconnu',
        ];

        if ($text === '') {
            return $result;
        }

        // Comptable / factures / devis / notes de frais
        if (
            str_contains($text, 'facture') ||
            str_contains($text, 'invoice') ||
            str_contains($text, 'tva') ||
            str_contains($text, 'ht ') ||
            str_contains($text, 'ttc')
        ) {
            $result['domain'] = 'comptable';
            $result['type'] = 'facture';
            return $result;
        }

        if (
            str_contains($text, 'devis') ||
            str_contains($text, 'quotation')
        ) {
            $result['domain'] = 'comptable';
            $result['type'] = 'devis';
            return $result;
        }

        if (
            str_contains($text, 'note de frais') ||
            str_contains($text, 'expense report')
        ) {
            $result['domain'] = 'comptable';
            $result['type'] = 'note_frais';
            return $result;
        }

        // Entreprise
        if (str_contains($text, 'kbis') || str_contains($text, 'siren')) {
            $result['domain'] = 'business';
            $result['type'] = 'kbis';
            return $result;
        }

        if (str_contains($text, 'statuts') && str_contains($text, 'société')) {
            $result['domain'] = 'business';
            $result['type'] = 'statuts';
            return $result;
        }

        if (str_contains($text, 'contrat de travail')) {
            $result['domain'] = 'business';
            $result['type'] = 'contrat_travail';
            return $result;
        }

        // Association
        if (str_contains($text, 'association') && str_contains($text, 'statuts')) {
            $result['domain'] = 'association';
            $result['type'] = 'statuts_association';
            return $result;
        }

        if (str_contains($text, 'procès-verbal') || str_contains($text, 'pv d\'assemblée')) {
            $result['domain'] = 'association';
            $result['type'] = 'pv_assemblee';
            return $result;
        }

        // Juridique simple
        if (str_contains($text, 'attestation')) {
            $result['domain'] = 'legal';
            $result['type'] = 'attestation';
            return $result;
        }

        if (str_contains($text, 'déclaration sur l\'honneur')) {
            $result['domain'] = 'legal';
            $result['type'] = 'declaration_honneur';
            return $result;
        }

        if (str_contains($text, 'contrat')) {
            $result['domain'] = 'legal';
            $result['type'] = 'contrat_simple';
            return $result;
        }

        // Administratif (CAF, CNPS, etc.)
        if (
            str_contains($text, 'caf') ||
            str_contains($text, 'cnps') ||
            str_contains($text, 'inss') ||
            str_contains($text, 'social security') ||
            str_contains($text, 'allocations') ||
            str_contains($text, 'aide au logement')
        ) {
            $result['domain'] = 'admin';
            $result['type'] = 'formulaire_admin';
            return $result;
        }

        return $result;
    }
}
