<?php

namespace App\IA\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAiClient implements IaClientInterface
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
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es MIDDO OS + IA, un système structuré, fiable et précis.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $options['temperature'] ?? 0.2,
                ],
                'timeout' => 30,
            ]);

            $data = $response->toArray(false);

            return $data['choices'][0]['message']['content'] ?? 'Réponse IA vide.';
        } catch (\Throwable $e) {
            return "Erreur OpenAI : " . $e->getMessage();
        }
    }
}
