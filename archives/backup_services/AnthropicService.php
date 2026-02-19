<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class AnthropicService
{
    private string $apiKey;
    private LoggerInterface $logger;
    private string $apiUrl = 'https://api.anthropic.com/v1/messages';
    
    // Statistiques d'appels
    private int $totalCalls = 0;
    private int $successCalls = 0;
    private int $errorCalls = 0;

    public function __construct(string $apiKey, LoggerInterface $logger)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    /**
     * Trouve des profils professionnels correspondant à un critère
     * Utilisé par l'API Matching
     */
    public function findProfiles(string $criteria): array
    {
        $this->logger->info('AnthropicService: Recherche de profils', ['criteria' => $criteria]);

        $prompt = "Tu es un expert en matching professionnel pour MIDDO, une plateforme collaborative.
        
Critères de recherche: {$criteria}

Génère EXACTEMENT 5 profils professionnels pertinents au format JSON suivant:
[
  {
    \"name\": \"Prénom Nom\",
    \"role\": \"Poste exact\",
    \"skills\": [\"compétence1\", \"compétence2\", \"compétence3\"],
    \"match_score\": 95,
    \"bio\": \"Description courte (50 mots max)\"
  }
]

IMPORTANT: Réponds UNIQUEMENT avec le tableau JSON, sans texte avant ou après.";

        try {
            $response = $this->callAnthropic($prompt, 'claude-3-haiku-20240307');
            
            // Nettoyer la réponse (supprimer markdown, espaces, etc.)
            $cleanResponse = trim($response);
            $cleanResponse = preg_replace('/^```json\s*/i', '', $cleanResponse);
            $cleanResponse = preg_replace('/\s*```$/i', '', $cleanResponse);
            
            $profiles = json_decode($cleanResponse, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('Erreur parsing JSON profils', [
                    'error' => json_last_error_msg(),
                    'response' => substr($cleanResponse, 0, 200)
                ]);
                throw new \Exception('Réponse Anthropic invalide: ' . json_last_error_msg());
            }
            
            $this->logger->info('Profils générés avec succès', ['count' => count($profiles)]);
            return $profiles;
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur findProfiles Anthropic', [
                'message' => $e->getMessage(),
                'criteria' => $criteria
            ]);
            throw $e; // Propager l'erreur pour debugging
        }
    }

    /**
     * Génère une réponse de chatbot
     * Utilisé par l'API Chatbot
     */
    public function generateChatResponse(string $userMessage): string
    {
        $this->logger->info('AnthropicService: Génération réponse chatbot', [
            'message' => substr($userMessage, 0, 50)
        ]);

        $prompt = "Tu es l'assistant IA de MIDDO, une plateforme collaborative innovante.
        
MIDDO connecte entrepreneurs, freelancers, investisseurs et professionnels pour collaborer sur des projets.

Message utilisateur: {$userMessage}

Réponds de manière amicale, professionnelle et concise (maximum 3 phrases).
Encourage la collaboration et l'utilisation des fonctionnalités MIDDO.";

        try {
            $response = $this->callAnthropic($prompt, 'claude-3-haiku-20240307');
            $this->logger->info('Réponse chatbot générée', [
                'length' => strlen($response)
            ]);
            return $response;
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur generateChatResponse', [
                'message' => $e->getMessage(),
                'user_message' => $userMessage
            ]);
            // Retourner l'erreur détaillée pour diagnostic
            return 'ERREUR ANTHROPIC: ' . $e->getMessage() . ' | Code: ' . $e->getCode();
        }
    }

    /**
     * Appel générique à l'API Anthropic
     * FIX CRITIQUE: Utilise json_encode avec JSON_UNESCAPED_UNICODE pour éviter les problèmes d'encodage
     */
    private function callAnthropic(string $prompt, string $model = 'claude-3-haiku-20240307'): string
    {
        $this->totalCalls++;
        
        $startTime = microtime(true);
        
        $payload = [
            'model' => $model,
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: 2023-06-01'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE), // FIX CRITIQUE
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        if ($curlError) {
            $this->errorCalls++;
            $this->logger->error('Erreur cURL Anthropic', [
                'error' => $curlError,
                'duration_ms' => $duration
            ]);
            throw new \Exception("Erreur réseau Anthropic: {$curlError}");
        }

        if ($httpCode !== 200) {
            $this->errorCalls++;
            $errorData = json_decode($response, true);
            $errorMessage = $errorData['error']['message'] ?? 'Erreur inconnue';
            
            $this->logger->error('Erreur HTTP Anthropic', [
                'http_code' => $httpCode,
                'error' => $errorMessage,
                'duration_ms' => $duration
            ]);
            
            throw new \Exception("Anthropic API HTTP {$httpCode}: {$errorMessage}", $httpCode);
        }

        $data = json_decode($response, true);

        if (!isset($data['content'][0]['text'])) {
            $this->errorCalls++;
            $this->logger->error('Réponse Anthropic invalide', [
                'response' => substr($response, 0, 200)
            ]);
            throw new \Exception('Format de réponse Anthropic invalide');
        }

        $this->successCalls++;
        $this->logger->info('Appel Anthropic réussi', [
            'model' => $model,
            'http_code' => $httpCode,
            'duration_ms' => $duration,
            'stats' => $this->getStats()
        ]);

        return $data['content'][0]['text'];
    }

    /**
     * Retourne les statistiques d'utilisation
     */
    public function getStats(): array
    {
        return [
            'total' => $this->totalCalls,
            'success' => $this->successCalls,
            'errors' => $this->errorCalls,
            'success_rate' => $this->totalCalls > 0 
                ? round(($this->successCalls / $this->totalCalls) * 100, 2) 
                : 0
        ];
    }
}
