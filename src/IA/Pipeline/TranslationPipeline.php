<?php

namespace App\IA\Pipeline;

use App\IA\AiKernel;
use App\AI\DTO\AIResponse;

final class TranslationPipeline
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    public function translate(string $text, string $targetLang = 'fr', string $model = 'gpt-4o'): AIResponse
    {
        $prompt = <<<TXT
Traduis le texte suivant en {$targetLang} :

$text

Règles :
- traduction naturelle
- pas mot-à-mot
- respecte le ton
- adapte les expressions culturelles
TXT;

        return $this->kernel->askBest($prompt, $model, 'quality');
    }
}
