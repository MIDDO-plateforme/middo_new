<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class ActionPlannerAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel
    ) {}

    public function getName(): string
    {
        return 'action-planner';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['planifier-actions', 'actions-plan', 'action-planner']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent de planification d'actions.

Entrée :
- Recommandations du Cortex
- Priorités
- Contexte

Ta mission :
- Transformer ces recommandations en un plan d'actions structuré
- Chaque action doit contenir :
  - ordre
  - type (administratif, document, tache, business, autre)
  - action (generer, remplir_formulaire, creer_tache, envoyer, organiser, etc.)
  - cible
  - deadline si applicable

Réponds en JSON strict :

{
  "plan": [
    {
      "ordre": 1,
      "type": "...",
      "action": "...",
      "cible": "...",
      "deadline": "..."
    }
  ]
}

Recommandations :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
