<?php

namespace App\Controller;

use App\Service\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

/**
 * HealthController - Complete health check endpoint
 * MIDDO Platform - SESSION 25+ (Enhanced from SESSION 30)
 */
class HealthController extends AbstractController
{
    private SearchService $searchService;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SearchService $searchService,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    ) {
        $this->searchService = $searchService;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        try {
            // Check Elasticsearch
            $isElasticsearchUp = $this->searchService->isElasticsearchAvailable();
            
            // Check Database
            $isDatabaseUp = $this->checkDatabase();
            
            // Check Cache
            $isCacheWritable = $this->checkCache();
            
            // Check Templates
            $areTemplatesPresent = $this->checkTemplates();
            
            // Check Disk Space
            $diskSpace = $this->checkDiskSpace();
            
            // Overall status
            $allHealthy = $isElasticsearchUp && $isDatabaseUp && $isCacheWritable && $areTemplatesPresent && $diskSpace['available_gb'] > 1;
            
            return new JsonResponse([
                'status' => $allHealthy ? 'healthy' : 'degraded',
                'checks' => [
                    'elasticsearch' => $isElasticsearchUp ? 'up' : 'down',
                    'database' => $isDatabaseUp ? 'up' : 'down',
                    'cache' => $isCacheWritable ? 'writable' : 'not_writable',
                    'templates' => $areTemplatesPresent ? 'present' : 'missing',
                    'disk_space' => $diskSpace
                ],
                'version' => '1.0.0',
                'deployment' => [
                    'environment' => $this->getParameter('kernel.environment'),
                    'debug' => $this->getParameter('kernel.debug')
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ], $allHealthy ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE);
            
        } catch (\Exception $e) {
            $this->logger->error('Health check failed: ' . $e->getMessage());
            return new JsonResponse([
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    private function checkDatabase(): bool
    {
        try {
            $connection = $this->entityManager->getConnection();
            $connection->executeQuery('SELECT 1');
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Database check failed: ' . $e->getMessage());
            return false;
        }
    }

    private function checkCache(): bool
    {
        try {
            $cacheDir = $this->getParameter('kernel.cache_dir');
            return is_writable($cacheDir);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkTemplates(): bool
    {
        try {
            $projectDir = $this->getParameter('kernel.project_dir');
            $templates = [
                '/templates/project/index.html.twig',
                '/templates/project/show.html.twig',
                '/templates/project/new.html.twig',
                '/templates/project/edit.html.twig'
            ];
            
            foreach ($templates as $template) {
                if (!file_exists($projectDir . $template)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkDiskSpace(): array
    {
        try {
            $projectDir = $this->getParameter('kernel.project_dir');
            $totalSpace = disk_total_space($projectDir);
            $freeSpace = disk_free_space($projectDir);
            
            return [
                'total_gb' => round($totalSpace / 1073741824, 2),
                'available_gb' => round($freeSpace / 1073741824, 2),
                'used_percent' => round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2)
            ];
        } catch (\Exception $e) {
            return [
                'total_gb' => 0,
                'available_gb' => 0,
                'used_percent' => 0
            ];
        }
    }
}
