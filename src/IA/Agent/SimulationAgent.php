<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class SimulationAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'simulation';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['simulation', 'what-if', 'scenario']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent de simulation de scénarios.

Ta mission :
- Explorer des scénarios "et si..." à partir d'un contexte donné.
- Comparer plusieurs options possibles.
- Pour chaque scénario :
  - décrire les conséquences probables
  - évaluer les risques
  - évaluer les bénéfices
- Produire un JSON structuré :
{
  "scenarios": [
    {
      "nom": "...",
      "description": "...",
      "risques": "...",
      "benefices": "...",
      "recommandation": "..."
    }
  ]
}

Contexte et hypothèses :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
