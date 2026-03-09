<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class ActionSafetyAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel
    ) {}

    public function getName(): string
    {
        return 'action-safety';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['verifier-actions', 'actions-safety', 'action-safety']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent de sécurité des actions.

Entrée :
- Un plan d'actions JSON

Ta mission :
- Vérifier :
  - cohérence
  - permissions
  - risques
  - informations manquantes
- Bloquer les actions dangereuses
- Autoriser les actions sûres

Réponds en JSON strict :

{
  "etat": "valide|bloque",
  "alertes": ["...", "..."],
  "actions_autorisees": [1, 2, 3]
}

Plan d'actions :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
