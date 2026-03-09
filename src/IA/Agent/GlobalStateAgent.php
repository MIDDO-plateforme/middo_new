<?php

namespace App\IA\Agent;

use App\IA\AiKernel;
use App\IA\Cortex\CortexState;

class GlobalStateAgent implements IaAgentInterface
{
    public function __construct(
        private AiKernel $kernel,
        private CortexState $cortex
    ) {}

    public function getName(): string
    {
        return 'global-state';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['etat-global', 'global-state', 'cortex-etat']);
    }

    public function process(string $task, string $input): string
    {
        $currentState = $this->cortex->getAll();
        $stateJson = json_encode($currentState, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Tu es un agent d'état global.

Tu reçois :
- l'état actuel du système (CORTEX)
- une demande utilisateur

Ta mission :
- Synthétiser l'état global (flux, analyses, prédictions, simulations, pipelines).
- Identifier les points importants (urgences, risques, opportunités, blocages).
- Proposer une vue d'ensemble claire.
- Suggérer les prochaines actions prioritaires.

Etat actuel (JSON) :
$stateJson

Demande utilisateur :
$input

Tu dois répondre en JSON :
{
  "resume": "...",
  "points_cles": ["...", "..."],
  "prochaines_actions": ["...", "..."]
}
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
