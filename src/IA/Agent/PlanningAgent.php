<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class PlanningAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'planning';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['plan', 'planning', 'objectif', 'objectif-complexe']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent planificateur expert.

Ta mission :
- Transformer l'objectif suivant en un plan clair, structuré, logique.
- Le plan doit être découpé en étapes numérotées.
- Chaque étape doit être concise, actionnable, et indépendante.
- Le format doit être strictement en JSON :
{
  "objectif": "...",
  "etapes": [
    {"numero": 1, "description": "..."},
    {"numero": 2, "description": "..."}
  ]
}

Objectif utilisateur :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
