<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class PredictiveAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'predictive';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['predictif', 'predictive', 'anticipation']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent prédictif.

Ta mission :
- À partir de flux organisés et/ou d'un contexte utilisateur,
  anticiper :
  - la charge de travail future
  - les risques potentiels
  - les opportunités à saisir
- Proposer des scénarios possibles (court terme, moyen terme).
- Produire un JSON structuré avec :
  - "scenarios"
  - "risques"
  - "opportunites"
  - "recommandations"

Contexte fourni :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
