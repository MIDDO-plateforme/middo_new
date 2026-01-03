<?php

namespace App\Controller\Admin;

use App\Service\SOIA\CircuitBreakerService;
use App\Service\SOIA\Fallback\FallbackManager;
use App\Service\SOIA\HealthCheck\HealthCheckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/soia', name: 'admin_soia_')]
class MonitoringController extends AbstractController
{
    #[Route('/status', name: 'status', methods: ['GET'])]
    public function status(
        HealthCheckService $healthCheck,
        CircuitBreakerService $circuitBreaker,
        FallbackManager $fallbackManager
    ): JsonResponse {
        $systemStatus = $healthCheck->getSystemStatus();
        $circuits = $circuitBreaker->getAllCircuits();
        
        return $this->json([
            'soia_version' => '1.0.0',
            'system_status' => $systemStatus,
            'circuits' => $circuits,
            'fallbacks' => [
                'total' => $fallbackManager->getTotalFallbacks(),
                'stats' => $fallbackManager->getStats()
            ],
            'uptime' => $this->calculateUptime($circuits),
            'timestamp' => time(),
            'server_time' => date('Y-m-d H:i:s')
        ]);
    }

    private function calculateUptime(array $circuits): float
    {
        if (empty($circuits)) return 100.0;
        
        $operational = count(array_filter($circuits, fn($c) => $c['state'] === 'CLOSED'));
        return round(($operational / count($circuits)) * 100, 2);
    }
}