<?php

namespace App\Service\Matching;

class MatchingEngine
{
    /**
     * Calcule un score de similarité simple entre deux tableaux de valeurs.
     *
     * Exemple : matching de compétences, intérêts, tags, etc.
     */
    public function similarity(array $a, array $b): float
    {
        if (empty($a) || empty($b)) {
            return 0.0;
        }

        $intersection = count(array_intersect($a, $b));
        $union = count(array_unique(array_merge($a, $b)));

        return $union > 0 ? $intersection / $union : 0.0;
    }
}
