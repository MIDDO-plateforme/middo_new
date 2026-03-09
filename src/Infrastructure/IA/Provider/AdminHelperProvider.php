<?php

namespace App\Infrastructure\IA\Provider;

class AdminHelperProvider
{
    public function analyze(string $title, ?string $country, ?string $description = null): array
    {
        $country = $country ? strtoupper($country) : null;
        $domain = $this->guessDomain($title, $description);

        $pieces = $this->defaultPiecesForDomain($domain);
        $steps = $this->defaultStepsForDomain($domain);

        return [
            'title' => $title,
            'country' => $country,
            'domain' => $domain,
            'description' => $description,
            'pieces' => $pieces,
            'steps' => $steps,
        ];
    }

    private function guessDomain(string $title, ?string $description): string
    {
        $text = mb_strtolower($title . ' ' . ($description ?? ''));

        if (str_contains($text, 'logement') || str_contains($text, 'loyer')) {
            return 'logement';
        }

        if (str_contains($text, 'famille') || str_contains($text, 'allocations') || str_contains($text, 'enfant')) {
            return 'famille';
        }

        if (str_contains($text, 'santé') || str_contains($text, 'maladie') || str_contains($text, 'assurance')) {
            return 'santé';
        }

        if (str_contains($text, 'impôt') || str_contains($text, 'taxe') || str_contains($text, 'fiscal')) {
            return 'impots';
        }

        if (str_contains($text, 'emploi') || str_contains($text, 'chômage') || str_contains($text, 'travail')) {
            return 'emploi';
        }

        return 'général';
    }

    private function defaultPiecesForDomain(string $domain): array
    {
        return match ($domain) {
            'logement' => [
                'Justificatif de domicile récent',
                'Pièce d’identité',
                'Justificatif de revenus',
            ],
            'famille' => [
                'Pièce d’identité',
                'Actes de naissance des enfants',
                'Justificatif de situation familiale',
            ],
            'santé' => [
                'Attestation de couverture santé',
                'Justificatif d’identité',
                'Éventuels certificats médicaux',
            ],
            'impots' => [
                'Justificatif de revenus',
                'Relevés bancaires si nécessaire',
                'Pièce d’identité',
            ],
            'emploi' => [
                'Contrat de travail ou attestations',
                'Bulletins de salaire',
                'Attestation de fin de contrat si chômage',
            ],
            default => [
                'Pièce d’identité',
                'Justificatif de domicile',
                'Justificatif de revenus si la démarche est financière',
            ],
        };
    }

    private function defaultStepsForDomain(string $domain): array
    {
        return [
            'Comprendre l’objectif de la démarche.',
            'Vérifier les conditions d’éligibilité dans le pays concerné.',
            'Rassembler les pièces justificatives nécessaires.',
            'Remplir le formulaire officiel (en ligne ou papier).',
            'Envoyer le dossier par le canal prévu (en ligne, courrier, dépôt).',
            'Suivre la réponse de l’organisme et conserver les preuves.',
        ];
    }
}
