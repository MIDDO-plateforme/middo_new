<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ClaudeService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    
    // CHANGÉ : Utilise Claude Haiku 3 (visible sur ton dashboard)
    private const MODEL = 'claude-3-haiku-20240307';
    private const MAX_TOKENS = 1024;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $anthropicApiKey
    ) {}

    /**
     * Générer des suggestions de projets/tâches basées sur le contexte utilisateur
     */
    public function generateSuggestions(array $context): array
    {
        try {
            $prompt = $this->buildPrompt($context);

            $this->logger->info('Claude API Request', [
                'url' => self::API_URL,
                'model' => self::MODEL,
                'api_key_prefix' => substr($this->anthropicApiKey, 0, 20) . '...'
            ]);

            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'x-api-key' => $this->anthropicApiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::MODEL,
                    'max_tokens' => self::MAX_TOKENS,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ],
                'timeout' => 30
            ]);

            $statusCode = $response->getStatusCode();
            $this->logger->info('Claude API Response Status', ['status' => $statusCode]);

            if ($statusCode !== 200) {
                throw new \RuntimeException('API returned status: ' . $statusCode);
            }

            $data = $response->toArray();

            if (!isset($data['content'][0]['text'])) {
                $this->logger->error('Invalid Claude response structure', ['data' => $data]);
                throw new \RuntimeException('Invalid Claude API response structure');
            }

            return $this->parseClaudeResponse($data['content'][0]['text']);

        } catch (\Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $responseBody = $e->getResponse()->getContent(false);
            
            $this->logger->error('Claude API HTTP error', [
                'status' => $statusCode,
                'response' => $responseBody,
                'exception' => $e->getMessage()
            ]);

            throw new \RuntimeException("Claude API error (HTTP $statusCode): " . $responseBody);
            
        } catch (\Exception $e) {
            $this->logger->error('Claude API error: ' . $e->getMessage(), [
                'context' => $context,
                'exception' => $e
            ]);

            throw new \RuntimeException('Failed to generate suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Construire le prompt pour Claude
     */
    private function buildPrompt(array $context): string
    {
        $userProfile = $context['userProfile'] ?? 'utilisateur';
        $userProjects = $context['userProjects'] ?? [];
        $limit = $context['limit'] ?? 5;

        $projectsList = empty($userProjects) ? 'aucun projet en cours' : implode(', ', $userProjects);

        return <<<PROMPT
Tu es un assistant IA pour MIDDO, une plateforme de networking professionnel et de gestion de projets.

**Profil utilisateur :** {$userProfile}
**Projets actuels :** {$projectsList}

**Mission :** Suggère {$limit} idées de projets ou tâches pertinentes et innovantes pour cet utilisateur.

**Format de réponse (JSON strict) :**
```json
{
  "suggestions": [
    {
      "type": "project",
      "title": "Titre court et accrocheur",
      "description": "Description détaillée en 2-3 phrases",
      "priority": "high",
      "estimatedDuration": "2 semaines",
      "tags": ["tag1", "tag2"],
      "reasoning": "Pourquoi cette suggestion est pertinente"
    }
  ]
}
```

**Critères :**
- Suggestions contextuelles et adaptées au profil
- Innovantes mais réalisables
- Variété dans les types (projets créatifs, techniques, collaboratifs)
- Estimations réalistes

Réponds UNIQUEMENT avec le JSON, sans texte avant ou après.
PROMPT;
    }

    /**
     * Parser la réponse de Claude
     */
    private function parseClaudeResponse(string $response): array
    {
        // Extraire le JSON de la réponse (Claude peut ajouter du texte autour)
        if (preg_match('/```json\s*(\{.*?\})\s*```/s', $response, $matches)) {
            $jsonString = $matches[1];
        } elseif (preg_match('/(\{.*\})/s', $response, $matches)) {
            $jsonString = $matches[1];
        } else {
            $jsonString = $response;
        }

        $data = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('JSON parse error', [
                'response' => $response,
                'error' => json_last_error_msg()
            ]);
            throw new \RuntimeException('Invalid JSON response from Claude: ' . json_last_error_msg());
        }

        if (!isset($data['suggestions']) || !is_array($data['suggestions'])) {
            throw new \RuntimeException('Missing suggestions array in Claude response');
        }

        return $data;
    }

    /**
     * Analyser le sentiment d'un texte
     */
    public function analyzeSentiment(string $text): array
    {
        try {
            $prompt = <<<PROMPT
Analyse le sentiment du texte suivant et réponds en JSON strict :

**Texte :** {$text}

**Format de réponse :**
```json
{
  "sentiment": "positive",
  "score": 0.8,
  "emotions": ["joie", "confiance"],
  "summary": "Texte positif exprimant de l'enthousiasme"
}
```

Réponds UNIQUEMENT avec le JSON.
PROMPT;

            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'x-api-key' => $this->anthropicApiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::MODEL,
                    'max_tokens' => 512,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ]
                ]
            ]);

            $data = $response->toArray();
            return $this->parseClaudeResponse($data['content'][0]['text']);

        } catch (\Exception $e) {
            $this->logger->error('Sentiment analysis error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to analyze sentiment: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier que l'API est accessible
     */
    public function healthCheck(): bool
    {
        try {
            $this->logger->info('Claude health check started');
            
            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'x-api-key' => $this->anthropicApiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => self::MODEL,
                    'max_tokens' => 10,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Test'
                        ]
                    ]
                ],
                'timeout' => 10
            ]);

            $isHealthy = $response->getStatusCode() === 200;
            $this->logger->info('Claude health check result', ['healthy' => $isHealthy]);
            
            return $isHealthy;
            
        } catch (\Exception $e) {
            $this->logger->error('Claude health check failed', [
                'error' => $e->getMessage(),
                'class' => get_class($e)
            ]);
            return false;
        }
    }
}
