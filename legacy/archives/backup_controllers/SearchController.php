<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SearchService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/search')]
#[IsGranted('ROLE_USER')]
class SearchController extends AbstractController
{
    public function __construct(
        private readonly SearchService $searchService,
        private readonly LoggerInterface $logger,
        private readonly RateLimiterFactory $apiSearchLimiter  // ✅ NOM CORRIGÉ (searchLimiterLimiter → apiSearchLimiter)
    ) {}

    #[Route('', name: 'search_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $limiter = $this->apiSearchLimiter->create($request->getClientIp());  // ✅ CORRIGÉ
        
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'success' => false,
                'error' => 'Trop de requêtes. Veuillez patienter.'
            ], 429);
        }

        $query = $request->get('q', '');
        $page = max(1, (int) $request->get('page', 1));
        $limit = min(50, max(10, (int) $request->get('limit', 20)));

        if (empty($query)) {
            return $this->render('search/index.html.twig', [
                'query' => '',
                'results' => [],
                'total' => 0,
                'page' => 1,
                'limit' => $limit
            ]);
        }

        try {
            $results = $this->searchService->search(
                $query,
                $this->getUser(),
                $page,
                $limit
            );

            if ($request->isXmlHttpRequest() || $request->getContentType() === 'json') {
                return $this->json([
                    'success' => true,
                    'results' => $results['results'],
                    'total' => $results['total'],
                    'page' => $page,
                    'limit' => $limit
                ]);
            }

            return $this->render('search/index.html.twig', [
                'query' => $query,
                'results' => $results['results'],
                'total' => $results['total'],
                'page' => $page,
                'limit' => $limit
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Search error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            if ($request->isXmlHttpRequest() || $request->getContentType() === 'json') {
                return $this->json([
                    'success' => false,
                    'error' => 'Erreur lors de la recherche'
                ], 500);
            }

            $this->addFlash('error', 'Une erreur est survenue lors de la recherche.');
            
            return $this->render('search/index.html.twig', [
                'query' => $query,
                'results' => [],
                'total' => 0,
                'page' => 1,
                'limit' => $limit
            ]);
        }
    }

    #[Route('/api', name: 'search_api', methods: ['GET', 'POST'])]
    public function api(Request $request): JsonResponse
    {
        $limiter = $this->apiSearchLimiter->create($request->getClientIp());  // ✅ CORRIGÉ
        
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'success' => false,
                'error' => 'Rate limit exceeded'
            ], 429);
        }

        $query = $request->get('q', '');
        
        if (empty($query)) {
            return $this->json([
                'success' => false,
                'error' => 'Query parameter is required'
            ], 400);
        }

        $page = max(1, (int) $request->get('page', 1));
        $limit = min(50, max(10, (int) $request->get('limit', 20)));

        try {
            $results = $this->searchService->search(
                $query,
                $this->getUser(),
                $page,
                $limit
            );

            return $this->json([
                'success' => true,
                'results' => $results['results'],
                'total' => $results['total'],
                'page' => $page,
                'limit' => $limit,
                'has_more' => ($page * $limit) < $results['total']
            ]);

        } catch (\Exception $e) {
            $this->logger->error('API Search error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Search service temporarily unavailable'
            ], 500);
        }
    }

    #[Route('/health', name: 'search_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        try {
            $status = $this->searchService->healthCheck();
            
            return $this->json([
                'success' => true,
                'status' => $status,
                'timestamp' => time()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Search health check failed', [
                'error' => $e->getMessage()
            ]);

            return $this->json([
                'success' => false,
                'status' => 'unavailable',
                'timestamp' => time()
            ], 503);
        }
    }

    #[Route('/suggest', name: 'search_suggest', methods: ['GET'])]
    public function suggest(Request $request): JsonResponse
    {
        $limiter = $this->apiSearchLimiter->create($request->getClientIp());  // ✅ CORRIGÉ
        
        if (!$limiter->consume(1)->isAccepted()) {
            return $this->json([
                'success' => false,
                'error' => 'Rate limit exceeded'
            ], 429);
        }

        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return $this->json([
                'success' => true,
                'suggestions' => []
            ]);
        }

        try {
            $suggestions = $this->searchService->suggest(
                $query,
                $this->getUser(),
                10
            );

            return $this->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Search suggest error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return $this->json([
                'success' => false,
                'suggestions' => []
            ]);
        }
    }
}
