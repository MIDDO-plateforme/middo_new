<?php

namespace App\IA\Pipeline;

use App\IA\AiKernel;
use App\AI\DTO\AIResponse;

final class ProjectPipeline
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    public function plan(string $goal, string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Crée un plan de projet détaillé pour atteindre cet objectif :

$goal

Inclure :
- étapes
- ressources
- risques
- calendrier
- livrables
TXT;

        return $this->kernel->askBest($prompt, $model, 'balanced');
    }
}
