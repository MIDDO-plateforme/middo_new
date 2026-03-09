<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class WorkflowAgent implements IaAgentInterface
{
    private AgentRouter $router;

    public function __construct(
        private AiKernel $kernel
    ) {}

    public function setRouter(AgentRouter $router): void
    {
        $this->router = $router;
    }

    public function getName(): string
    {
        return 'workflow';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['workflow', 'multi-etapes', 'execution-plan']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent d'exécution de plan.

L'utilisateur fournit un plan au format JSON, contenant :
- un objectif
- une liste d'étapes numérotées

Ta mission :
- Lire le plan
- Exécuter chaque étape une par une
- Pour chaque étape, déterminer quel agent est le plus adapté
- Appeler cet agent via le router interne
- Assembler les résultats dans un JSON final :

{
  "objectif": "...",
  "resultats": [
    {"etape": 1, "description": "...", "resultat": "..."},
    {"etape": 2, "description": "...", "resultat": "..."}
  ]
}

Plan fourni :
$input

PROMPT;

        return $this->kernel->generate($prompt);
    }
}
