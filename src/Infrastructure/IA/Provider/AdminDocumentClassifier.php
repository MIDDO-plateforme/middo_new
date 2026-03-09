<?php

namespace App\Infrastructure\IA\Provider;

class AdminDocumentClassifier
{
    public function classify(string $visionText): array
    {
        $text = mb_strtolower($visionText);

        // Pays
        $country = null;
        if (str_contains($text, 'caf') || str_contains($text, 'allocations familiales')) {
            $country = 'FR';
        }
        if (str_contains($text, 'cnps')) {
            $country = 'CI';
        }
        if (str_contains($text, 'inss')) {
            $country = 'BR';
        }
        if (str_contains($text, 'social security')) {
            $country = 'US';
        }

        // Domaine
        $domain = null;
        if (str_contains($text, 'logement') || str_contains($text, 'apl')) {
            $domain = 'logement';
        }
        if (str_contains($text, 'famille') || str_contains($text, 'allocations')) {
            $domain = 'famille';
        }
        if (str_contains($text, 'santé') || str_contains($text, 'assurance maladie')) {
            $domain = 'santé';
        }

        return [
            'country' => $country,
            'domain' => $domain,
        ];
    }
}
