<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AutoResolve\DetectorService;
use App\Service\AutoResolve\HealthCheckService;
use App\Service\AutoResolve\LogAnalyzer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/workspaces', name: 'api_workspaces_')]
class WorkspaceController extends AbstractController
{
    public function __construct(
        private readonly DetectorService $detector,
        private readonly HealthCheckService $healthCheck,
        private readonly LogAnalyzer $logAnalyzer,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json([
            [
                'id' => 1,
                'code' => 'default',
                'label' => 'Espace de travail principal',
                'status' => 'active',
            ],
        ]);
    }

    #[Route('/detect', name: 'detect', methods: ['GET'])]
    public function detect(Request $request): JsonResponse
    {
        $errors = $this->detector->detectCriticalIssues();
        
        return $this->json([
            'context' => [
                'ip' => $request->getClientIp(),
                'userAgent' => $request->headers->get('User-Agent'),
            ],
            'errors' => $errors,
        ]);
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $status = $this->healthCheck->runAll();

        return $this->json([
            'status' => 'ok',
            'details' => $status,
            'checked_at' => (new \DateTimeImmutable())->format(\DATE_ATOM),
        ]);
    }

    #[Route('/logs/analyze', name: 'logs_analyze', methods: ['GET'])]
    public function analyzeLogs(): JsonResponse
    {
        $errors = $this->logAnalyzer->extractErrors();

        return $this->json([
            'total' => count($errors),
            'errors' => $errors,
        ]);
    }
}
