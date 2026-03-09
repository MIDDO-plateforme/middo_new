<?php

namespace App\IA\Agent;

use App\IA\AiKernel;

class MetaAgent implements IaAgentInterface
{
    public function __construct(private AiKernel $kernel) {}

    public function getName(): string
    {
        return 'meta';
    }

    public function supports(string $task): bool
    {
        return in_array($task, ['meta', 'analyse-agents', 'optimisation']);
    }

    public function process(string $task, string $input): string
    {
        $prompt = <<<PROMPT
Tu es un agent méta.

Ta mission :
- Observer les interactions
- Analyser les agents utilisés
- Détecter les patterns
- Proposer des améliorations
- Optimiser les prompts internes
- Suggérer de nouveaux agents si nécessaire

Contexte :
$input
PROMPT;

        return $this->kernel->generate($prompt);
    }
}
