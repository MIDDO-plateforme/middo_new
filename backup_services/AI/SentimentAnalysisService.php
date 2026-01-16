<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class SentimentAnalysisService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $openaiApiKey;
    private string $anthropicApiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $openaiApiKey,
        string $anthropicApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->openaiApiKey = $openaiApiKey;
        $this->anthropicApiKey = $anthropicApiKey;
    }

    public function analyzeSentiment(string $text): array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Analyze sentiment. Return JSON: {"sentiment":"positive|negative|neutral","score":0.8,"confidence":0.9,"emotions":["joy"],"keywords":["word1"]}'],
                        ['role' => 'user', 'content' => $text]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 500
                ]
            ]);

            $data = $response->toArray();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $result = json_decode(trim($content), true);

            return [
                'success' => true,
                'sentiment' => $result['sentiment'] ?? 'neutral',
                'score' => $result['score'] ?? 0,
                'confidence' => $result['confidence'] ?? 0,
                'emotions' => $result['emotions'] ?? [],
                'keywords' => $result['keywords'] ?? [],
                'provider' => 'openai',
                'timestamp' => new \DateTime()
            ];
        } catch (\Exception $e) {
            $this->logger->error('Sentiment analysis failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'sentiment' => 'neutral',
                'score' => 0,
                'confidence' => 0,
                'emotions' => [],
                'keywords' => [],
                'provider' => 'fallback',
                'timestamp' => new \DateTime()
            ];
        }
    }
}
