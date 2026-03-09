<?php

namespace App\IA\Agent;

use App\IA\AiKernel;
use App\IA\Cortex\CortexState;

class VisionAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private CortexState $cortex
    ) {}

    public function getName(): string
    {
        return 'vision';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['vision', 'strategie', 'trajectoire']);
    }

    public function process(string $task, string $input): string
    {
        $currentState = $this->cortex->getAll();
        $stateJson = json_encode($currentState, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Tu es un agent de vision stratégique.

Tu reçois :
- l'état global du système (CORTEX)
- un contexte ou une ambition utilisateur

Ta mission :
- Projeter des trajectoires possibles (court, moyen, long terme).
- Identifier les leviers, risques, opportunités.
- Proposer des axes stratégiques concrets.
- Produire un JSON structuré :

{
  "vision_court_terme": "...",
  "vision_moyen_terme": "...",
  "vision_long_terme": "...",
  "axes_strategiques": ["...", "..."],
  "risques_majeurs": ["...", "..."],
  "opportunites_majeures": ["...", "..."]
}

Etat global (JSON) :
$stateJson

Contexte / ambition :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
