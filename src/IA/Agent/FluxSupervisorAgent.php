<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class FluxSupervisorAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'flux-supervisor';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['flux-supervision', 'regulation-flux', 'controle-flux']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent superviseur de flux.

Ta mission :
- Surveiller la quantité et la nature des flux reçus.
- Détecter les surcharges potentielles (volume, complexité, urgence).
- Proposer une régulation :
  - ce qui doit être traité en priorité
  - ce qui peut être différé
  - ce qui doit être archivé
- Garantir que le système reste fluide, non contraignant pour l'utilisateur.
- Produire un JSON de régulation + un résumé clair.

Format JSON attendu :
{
  "etat_global": "fluide|charge|sature",
  "alertes": [
    {"type": "volume|urgence|risque", "message": "..."}
  ],
  "priorites": [
    {"niveau": "haute|normale|basse", "description": "..."}
  ],
  "actions_suggerees": [
    "..."
  ]
}

Contexte des flux :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
