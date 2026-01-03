<?php

namespace App\Controller;

use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

/**
 * HealthController - Health check endpoint
 * MIDDO Platform - SESSION 30
 */
class HealthController extends AbstractController
{
    private SearchService $searchService;
    private LoggerInterface $logger;

    public function __construct(
        SearchService $searchService,
        LoggerInterface $logger
    ) {
        $this->searchService = $searchService;
        $this->logger = $logger;
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        try {
            $isElasticsearchUp = $this->searchService->isElasticsearchAvailable();
            
            return new JsonResponse([
                'status' => $isElasticsearchUp ? 'healthy' : 'degraded',
                'elasticsearch' => $isElasticsearchUp ? 'up' : 'down',
                'timestamp' => time()
            ], $isElasticsearchUp ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Exception $e) {
            $this->logger->error('Health check failed: ' . $e->getMessage());
            return new JsonResponse([
                'status' => 'unhealthy',
                'elasticsearch' => 'error',
                'error' => $e->getMessage(),
                'timestamp' => time()
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}