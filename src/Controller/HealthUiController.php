<?php

namespace App\Controller;

use App\Service\HealthCheckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HealthUiController extends AbstractController
{
    #[Route('/healthz/ui', name: 'health_ui', methods: ['GET'])]
    public function __invoke(HealthCheckService $healthCheckService): Response
    {
        $data = $healthCheckService->getFullStatus();

        return $this->render('health/index.html.twig', [
            'data' => $data,
        ]);
    }
}
