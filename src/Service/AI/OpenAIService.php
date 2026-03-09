<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiKey
    ) {}

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function createSystemMessage(string $content): array
    {
        return [
            'role' => 'system',
            'content' => $content,
        ];
    }

    public function createUserMessage(string $content): array
    {
        return [
            'role' => 'user',
            'content' => $content,
        ];
    }

    public function chatJson(array $messages, string $model = 'gpt-4o-mini'): array
    {
        return [
            'matches' => [],
            'total_analyses' => 0,
        ];
    }

    public function chat(string $prompt, array $context = []): string
    {
        return 'AI response for: ' . $prompt;
    }
}
