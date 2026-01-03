<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Service\OpenAIService;
use App\Service\FallbackManager;

#[Route('/api/sentiment')]
class SentimentController extends AbstractController
{
    private LoggerInterface $logger;
    private OpenAIService $openAIService;
    private FallbackManager $fallbackManager;

    public function __construct(
        LoggerInterface $logger,
        OpenAIService $openAIService,
        FallbackManager $fallbackManager
    ) {
        $this->logger = $logger;
        $this->openAIService = $openAIService;
        $this->fallbackManager = $fallbackManager;
    }

    #[Route('/analyze', name: 'api_sentiment_analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        try {
            $this->logger->info('🔄 [SENTIMENT] Tentative appel OpenAI GPT-4...');

            // Tentative appel OpenAI réel
            $sentimentData = $this->openAIService->analyzeSentiment();

            if ($sentimentData && isset($sentimentData['sentiment'])) {
                $this->logger->info('✅ [SENTIMENT] OpenAI réussi - ' . $sentimentData['sentiment']);
                return $this->json([
                    'success' => true,
                    'sentiment' => $sentimentData['sentiment'],
                    'emotion' => $sentimentData['emotion'] ?? 'neutre',
                    'confidence' => $sentimentData['confidence'] ?? 0.0,
                    'demo_mode' => false,
                    'provider' => 'OpenAI GPT-4'
                ]);
            }

            throw new \Exception('OpenAI retourné vide');

        } catch (\Throwable $e) {
            $this->logger->warning('⚠️ [SENTIMENT] OpenAI échec : ' . $e->getMessage());
            $this->logger->info('🔄 [SENTIMENT] Fallback vers mode DEMO...');

            // Fallback automatique vers DEMO
            $demoSentiment = $this->fallbackManager->getSentiment();

            return $this->json([
                'success' => true,
                'sentiment' => $demoSentiment['sentiment'],
                'emotion' => $demoSentiment['emotion'],
                'confidence' => $demoSentiment['confidence'],
                'demo_mode' => true,
                'provider' => 'SOIA Fallback',
                'fallback_reason' => $e->getMessage()
            ]);
        }
    }
}