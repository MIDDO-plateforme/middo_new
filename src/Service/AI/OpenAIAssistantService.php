<?php

namespace App\Service\AI;

use App\Service\AI\AIAgentInterface;
use App\Service\AI\AIAgentResponse;

class OpenAIAssistantService implements AIAssistantInterface, AIAgentInterface
{
    public function __construct(
        private OpenAIService $openAI
    ) {}

    public function ask(string $prompt, array $context = []): string
    {
        return $this->openAI->chat($prompt, $context);
    }

    public function handleIntent(string $intent, array $context = []): AIAgentResponse
    {
        $response = $this->ask($intent, $context);

        return new AIAgentResponse(
            type: 'external',
            message: $response,
            data: []
        );
    }
}
