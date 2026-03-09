<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class ActionExecutorAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel
    ) {}

    public function getName(): string
    {
        return 'action-executor';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['executer-actions', 'actions-execute', 'action-executor']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent d'exécution d'actions.

Entrée :
- Un plan d'actions validé
- Une liste d'actions autorisées

Ta mission :
- Simuler l'exécution des actions
- Produire un rapport d'exécution

Réponds en JSON strict :

{
  "execution": [
    {
      "action": 1,
      "statut": "ok|erreur",
      "details": "..."
    }
  ]
}

Plan validé :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
