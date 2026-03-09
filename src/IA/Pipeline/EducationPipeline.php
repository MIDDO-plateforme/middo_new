<?php

namespace App\IA\Pipeline;

use App\IA\AiKernel;
use App\AI\DTO\AIResponse;

final class EducationPipeline
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    public function explain(string $topic, string $level = 'beginner', string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Explique ce sujet de manière adaptée à un niveau {$level} :

$topic

Règles :
- phrases simples
- exemples concrets
- analogies
- structure claire
TXT;

        return $this->kernel->askBest($prompt, $model, 'pedagogical');
    }
}

