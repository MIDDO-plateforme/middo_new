<?php

namespace App\Service\AI;

class OpenAIAssistantService implements AIAssistantInterface
{
    public function __construct(
        private OpenAIService $openAI
    ) {}

    public function ask(string $prompt, array $context = []): string
    {
        return $this->openAI->chat($prompt, $context);
    }
}
