<?php

namespace App\Controller;

use App\Service\HealthCheckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class HealthApiController extends AbstractController
{
    #[Route('/healthz/light', name: 'health_light', methods: ['GET'])]
    public function light(): JsonResponse
    {
        return new JsonResponse(['status' => 'OK']);
    }

    #[Route('/healthz/full', name: 'health_full', methods: ['GET'])]
    public function full(HealthCheckService $healthCheckService): JsonResponse
    {
        return new JsonResponse(
            $healthCheckService->getFullStatus(),
            200,
            [],
            false // Laisse Symfony encoder l'array
        );
    }
}
