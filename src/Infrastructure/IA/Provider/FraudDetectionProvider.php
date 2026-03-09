<?php

namespace App\Infrastructure\IA\Provider;

class FraudDetectionProvider
{
    /**
     * Analyse un texte et renvoie un score de risque entre 0 et 1
     * + une liste de signaux.
     */
    public function analyze(string $text): array
    {
        $risk = 0.0;
        $signals = [];

        $lower = mb_strtolower($text);

        $patterns = [
            'argent' => 0.2,
            'virement' => 0.2,
            'urgent' => 0.2,
            'confidentiel' => 0.2,
            'mot de passe' => 0.3,
            'code de sécurité' => 0.3,
            'cliquer sur ce lien' => 0.3,
        ];

        foreach ($patterns as $word => $weight) {
            if (str_contains($lower, $word)) {
                $risk += $weight;
                $signals[] = "Mot clé suspect détecté : {$word}";
            }
        }

        $risk = min(1.0, $risk);

        return [
            'risk' => $risk,
            'signals' => $signals,
        ];
    }
}
