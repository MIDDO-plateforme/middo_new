<?php

namespace App\IA\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AnthropicClient implements IaClientInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
        private string $model,
    ) {
    }

    public function generate(string $prompt, array $options = []): string
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 500,
                ],
            ]);

            $data = $response->toArray(false);

            return $data['content'][0]['text'] ?? 'Réponse IA vide.';
        } catch (\Throwable $e) {
            return "Erreur Anthropic : " . $e->getMessage();
        }
    }
}
