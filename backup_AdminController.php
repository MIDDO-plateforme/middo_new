<?php

namespace App\Controller\Admin;

use App\Service\AdminDashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private AdminDashboardService $dashboardService
    ) {}

    #[Route('/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'stats' => $this->dashboardService->getOverallStats(),
        ]);
    }

    #[Route('/api/stats', name: 'admin_api_stats', methods: ['GET'])]
    public function apiStats(): JsonResponse
    {
        return $this->json($this->dashboardService->getApiStats());
    }

    #[Route('/api/elasticsearch', name: 'admin_elasticsearch_stats', methods: ['GET'])]
    public function elasticsearchStats(): JsonResponse
    {
        return $this->json($this->dashboardService->getElasticsearchStats());
    }

    #[Route('/api/redis', name: 'admin_redis_stats', methods: ['GET'])]
    public function redisStats(): JsonResponse
    {
        return $this->json($this->dashboardService->getRedisStats());
    }

    #[Route('/api/system', name: 'admin_system_stats', methods: ['GET'])]
    public function systemStats(): JsonResponse
    {
        return $this->json($this->dashboardService->getSystemStats());
    }

    #[Route('/cache/clear', name: 'admin_cache_clear', methods: ['POST'])]
    public function clearCache(): JsonResponse
    {
        try {
            $this->dashboardService->clearAllCaches();
            return $this->json([
                'success' => true,
                'message' => 'All caches cleared successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/elasticsearch/reindex', name: 'admin_elasticsearch_reindex', methods: ['POST'])]
    public function reindexElasticsearch(): JsonResponse
    {
        try {
            $this->dashboardService->reindexElasticsearch();
            return $this->json([
                'success' => true,
                'message' => 'Elasticsearch reindexing started'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/logs', name: 'admin_logs', methods: ['GET'])]
    public function logs(): Response
    {
        $logs = $this->dashboardService->getRecentLogs(100);
        
        return $this->render('admin/logs.html.twig', [
            'logs' => $logs,
        ]);
    }
}
