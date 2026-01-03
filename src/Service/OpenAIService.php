<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class OpenAIService
{
    private string $apiKey;
    private LoggerInterface $logger;

    public function __construct(string $apiKey, LoggerInterface $logger)
    {
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    public function generateSuggestions(): array
    {
        try {
            $this->logger->info('🔄 [OpenAI] Appel GPT-4 pour suggestions...');

            $response = $this->callOpenAI([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un assistant IA expert en gestion de projet. Génère exactement 5 suggestions SMART (Spécifiques, Mesurables, Atteignables, Réalistes, Temporellement définis) pour améliorer un projet. Retourne UNIQUEMENT un tableau JSON avec format: ["🎯 Suggestion 1", "💰 Suggestion 2", ...]'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Donne-moi 5 suggestions SMART pour améliorer mon projet de plateforme collaborative.'
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500
            ]);

            if (isset($response['choices'][0]['message']['content'])) {
                $content = $response['choices'][0]['message']['content'];
                $suggestions = json_decode($content, true);

                if (is_array($suggestions) && count($suggestions) >= 5) {
                    $this->logger->info('✅ [OpenAI] 5 suggestions générées');
                    return array_slice($suggestions, 0, 5);
                }
            }

            throw new \Exception('Format réponse OpenAI invalide');

        } catch (\Throwable $e) {
            $this->logger->error('❌ [OpenAI] Erreur : ' . $e->getMessage());
            return [];
        }
    }

    public function analyzeSentiment(): array
    {
        try {
            $this->logger->info('🔄 [OpenAI] Analyse sentiment GPT-4...');

            $response = $this->callOpenAI([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Analyse le sentiment d\'un message et retourne UNIQUEMENT un JSON avec format exact: {"sentiment": "positif|négatif|neutre", "emotion": "optimiste|pessimiste|neutre|excité|anxieux", "confidence": 0.85}'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Analyse le sentiment de ce message : "Je suis très motivé pour avancer sur ce projet avec mon équipe !"'
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 100
            ]);

            if (isset($response['choices'][0]['message']['content'])) {
                $content = $response['choices'][0]['message']['content'];
                $sentiment = json_decode($content, true);

                if (isset($sentiment['sentiment'])) {
                    $this->logger->info('✅ [OpenAI] Sentiment analysé : ' . $sentiment['sentiment']);
                    return $sentiment;
                }
            }

            throw new \Exception('Format réponse OpenAI invalide');

        } catch (\Throwable $e) {
            $this->logger->error('❌ [OpenAI] Erreur sentiment : ' . $e->getMessage());
            return [];
        }
    }

    private function callOpenAI(array $params): array
    {
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("OpenAI API erreur HTTP $httpCode");
        }

        return json_decode($response, true) ?? [];
    }
}