<?php

namespace App\IA\Agents;

use App\IA\Client\IaClientInterface;

class PlannerAgent
{
    public function __construct(
        private IaClientInterface $iaClient,
    ) {
    }

    public function plan(string $context = ''): array
    {
        $prompt = <<<PROMPT
Tu es l'agent Planner de MIDDO OS + IA.

Ta mission :
- analyser le contexte fourni
- identifier les tâches principales
- proposer un plan d'action structuré
- rester concis, clair, actionnable

Contexte :
{$context}

Réponds sous forme de liste de tâches numérotées, en français.
PROMPT;

        $raw = $this->iaClient->generate($prompt, [
            'role' => 'planner',
        ]);

        return [
            'raw' => $raw,
            'steps' => explode("\n", $raw),
            'status' => 'ok',
        ];
    }
}
