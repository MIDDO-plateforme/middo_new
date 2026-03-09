<?php

namespace App\Infrastructure\IA\Provider;

class TaskGuidanceProvider
{
    public function analyze(string $taskDescription, ?string $visionSummary = null): array
    {
        $steps = [
            'Comprendre la demande ou le formulaire.',
            'Identifier les informations nécessaires.',
            'Vérifier que les documents sont complets.',
            'Remplir étape par étape, sans se presser.',
        ];

        if ($visionSummary) {
            $steps[] = 'Vérifier que le document montré correspond bien à la demande.';
        }

        return [
            'task' => $taskDescription,
            'steps' => $steps,
            'next' => $steps[0] ?? null,
        ];
    }
}
