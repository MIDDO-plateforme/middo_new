<?php

namespace App\Infrastructure\IA\Provider;

class InsightProvider
{
    /**
     * Met en lumière des points à considérer, sans juger.
     */
    public function analyze(string $text): array
    {
        $insights = [];
        $lower = mb_strtolower($text);

        if (str_contains($lower, 'argent')) {
            $insights[] = 'L’argent semble être un point central, vérifier la sécurité et les engagements.';
        }

        if (str_contains($lower, 'travail')) {
            $insights[] = 'Le travail est en jeu, penser à l’équilibre entre santé, temps et revenus.';
        }

        if (empty($insights)) {
            $insights[] = 'Il peut être utile de clarifier ce qui est le plus important pour toi dans cette situation.';
        }

        return $insights;
    }
}
