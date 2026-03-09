<?php

namespace App\Infrastructure\IA\Provider;

class StrategyProvider
{
    /**
     * Prend une description de situation et renvoie une structure :
     * - contexte
     * - objectifs possibles
     * - options d'action
     */
    public function analyze(string $text): array
    {
        // Ici on fait simple : on structure sans IA externe.
        return [
            'context' => $text,
            'goals' => [
                'Clarifier ce que la personne veut vraiment.',
                'Identifier les contraintes principales.',
            ],
            'options' => [
                'Option 1 : agir rapidement avec les moyens actuels.',
                'Option 2 : prendre du temps pour mieux préparer.',
                'Option 3 : demander de l’aide à une personne de confiance.',
            ],
        ];
    }
}
