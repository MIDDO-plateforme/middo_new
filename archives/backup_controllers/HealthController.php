<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthController extends AbstractController
{
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
    
    #[Route('/health/db', name: 'app_health_db', methods: ['GET'])]
    public function healthDb(Connection $connection): JsonResponse
    {
        try {
            // Test simple de connexion DB
            $connection->executeQuery('SELECT 1');
            
            return new JsonResponse([
                'status' => 'ok',
                'database' => 'connected',
                'timestamp' => time()
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'database' => 'disconnected',
                'error' => $e->getMessage(),
                'timestamp' => time()
            ], 503);
        }
    }
}