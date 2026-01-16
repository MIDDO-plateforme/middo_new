<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SuggestionsService
{
    private string $claudeApiKey;
    private HttpClientInterface $httpClient;

    public function __construct(
        HttpClientInterface $httpClient,
        string $claudeApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->claudeApiKey = $claudeApiKey;
    }

    /**
     * Génère des suggestions IA pour un projet
     */
    public function generateSuggestions(string $projectTitle, string $projectDescription): array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key' => $this->claudeApiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ],
                'json' => [
                    'model' => 'claude-3-5-sonnet-20241022',
                    'max_tokens' => 300,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "Analyse ce projet et propose 5 suggestions d'amélioration claires et actionnables.

Titre du projet : $projectTitle

Description :
$projectDescription"
                        ]
                    ]
                ]
            ]);

            $data = $response->toArray();

            // Extraction du texte généré par Claude
            $content = $data['content'][0]['text'] ?? '';

            return [
                'success' => true,
                'suggestions' => $content,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
