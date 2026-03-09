<?php

namespace App\IA\Pipeline;

use App\IA\AiKernel;
use App\AI\DTO\AIResponse;

final class EmotionPipeline
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    public function detectEmotion(string $text, string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Analyse le texte suivant et détecte l'émotion dominante :

$text

Réponds avec :
- émotion principale
- intensité (1 à 10)
- justification
TXT;

        return $this->kernel->askBest($prompt, $model, 'analysis');
    }
}
