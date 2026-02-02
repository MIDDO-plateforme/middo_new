<?php

namespace App\Controller;

use App\Service\HealthCheckService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class HealthController
{
    private HealthCheckService $health;

    public function __construct(HealthCheckService $health)
    {
        $this->health = $health;
    }

    #[Route('/healthz', name: 'healthz', methods: ['GET'])]
    #[Route('/api/workspaces/health', name: 'workspace_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            $this->health->getFullStatus(),
            200
        );
    }
}
