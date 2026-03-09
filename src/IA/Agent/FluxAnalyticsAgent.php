<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class FluxAnalyticsAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'flux-analytics';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['flux-analyse', 'analyse-flux', 'rapport-flux']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent d'analyse avancée de flux.

Ta mission :
- Prendre un ensemble de flux organisés (JSON).
- Produire une analyse poussée selon plusieurs axes :
  - administratif
  - business
  - personnel
  - risque
  - opportunités
  - charge de travail
- Proposer des recommandations concrètes et priorisées.
- Produire un JSON structuré + un résumé lisible.

Format JSON attendu :
{
  "synthese": "...",
  "analyses": {
    "administratif": "...",
    "business": "...",
    "personnel": "...",
    "risques": "...",
    "opportunites": "...",
    "charge_travail": "faible|moyenne|elevee|critique"
  },
  "recommandations": [
    {"priorite": "haute|normale|basse", "action": "..."}
  ]
}

Flux organisés fournis :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
