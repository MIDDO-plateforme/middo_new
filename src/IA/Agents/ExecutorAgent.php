<?php

namespace App\IA\Agents;

use App\IA\Client\IaClientInterface;

class ExecutorAgent
{
    public function __construct(
        private IaClientInterface $iaClient,
    ) {
    }

    public function execute(string $context = ''): array
    {
        $prompt = <<<PROMPT
Tu es l'agent Executor de MIDDO OS + IA.

Ta mission :
- analyser le contexte (flux + plan + éventuelle validation safety)
- proposer une exécution simulée des actions
- NE PAS exécuter réellement, seulement décrire ce qui serait fait
- rester concret, étape par étape

Contexte :
{$context}

Réponds en texte structuré, en français, avec des étapes numérotées.
PROMPT;

        $raw = $this->iaClient->generate($prompt, [
            'role' => 'executor',
        ]);

        return [
            'status' => 'simulated',
            'details' => $raw,
        ];
    }
}
