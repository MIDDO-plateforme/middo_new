<?php

namespace App\Infrastructure\IA\Provider;

class VisionProvider
{
    public function __construct(
        private ?object $geminiClient = null,
        private ?object $openaiClient = null,
        private ?object $llavaClient = null,
    ) {}

    public function analyze(string $imageBase64, ?string $hint = null): string
    {
        $prompt = $hint ?? 'Décris précisément cette image et ce qui est important pour aider la personne.';

        if ($this->geminiClient) {
            return $this->callGemini($imageBase64, $prompt);
        }

        if ($this->openaiClient) {
            return $this->callOpenAI($imageBase64, $prompt);
        }

        if ($this->llavaClient) {
            return $this->callLLaVA($imageBase64, $prompt);
        }

        return 'Aucun moteur de vision disponible.';
    }

    private function callGemini(string $imageBase64, string $prompt): string
    {
        // Placeholder : ici tu branches ton client Gemini Vision
        return '[Gemini Vision] ' . $prompt;
    }

    private function callOpenAI(string $imageBase64, string $prompt): string
    {
        // Placeholder : ici tu branches GPT-4o Vision
        return '[GPT-4o Vision] ' . $prompt;
    }

    private function callLLaVA(string $imageBase64, string $prompt): string
    {
        // Placeholder : ici tu branches LLaVA local
        return '[LLaVA] ' . $prompt;
    }
}
