<?php

namespace App\IA\Agent;

use App\IA\AiKernel;
use App\IA\Cortex\CortexState;

class CortexMonitorAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private CortexState $cortex
    ) {}

    public function getName(): string
    {
        return 'cortex-monitor';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['cortex-monitor', 'surveillance-cortex', 'coherence-systeme']);
    }

    public function process(string $task, string $input): string
    {
        $state = $this->cortex->getAll();
        $stateJson = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Tu es un agent de surveillance du CORTEX (cohérence système).

Ta mission :
- Analyser l'état global du système (CORTEX).
- Détecter :
  - incohérences
  - surcharges
  - manques d'information
  - risques systémiques
- Proposer des corrections ou améliorations.
- Produire un JSON :

{
  "etat": "stable|degrade|critique",
  "alertes": ["...", "..."],
  "recommandations": ["...", "..."]
}

Etat actuel (JSON) :
$stateJson

Contexte / remarque utilisateur :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
