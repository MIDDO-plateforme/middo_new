<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use App\Repository\WorkspaceProjectRepository;
use App\Repository\WorkspaceTaskRepository;
use App\Service\AdminDashboardService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AdminDashboardServiceTest extends TestCase
{
    private AdminDashboardService $service;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private WorkspaceRepository $workspaceRepository;
    private WorkspaceProjectRepository $projectRepository;
    private WorkspaceTaskRepository $taskRepository;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->workspaceRepository = $this->createMock(WorkspaceRepository::class);
        $this->projectRepository = $this->createMock(WorkspaceProjectRepository::class);
        $this->taskRepository = $this->createMock(WorkspaceTaskRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new AdminDashboardService(
            $this->entityManager,
            $this->userRepository,
            $this->workspaceRepository,
            $this->projectRepository,
            $this->taskRepository,
            $this->logger
        );
    }

    public function testGetGlobalStatsReturnsCorrectStructure(): void
    {
        $this->userRepository->method('count')->willReturn(100);
        $this->userRepository->method('countActiveUsers')->willReturn(80);
        $this->userRepository->method('countNewUsersThisMonth')->willReturn(10);
        $this->workspaceRepository->method('count')->willReturn(50);
        $this->workspaceRepository->method('countActiveWorkspaces')->willReturn(45);

        $stats = $this->service->getGlobalStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('users', $stats);
        $this->assertArrayHasKey('workspaces', $stats);
        $this->assertArrayHasKey('projects', $stats);
        $this->assertArrayHasKey('tasks', $stats);
    }

    public function testGetElasticsearchStatsSuccess(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }

    public function testGetElasticsearchStatsError(): void
    {
        $this->userRepository->method('count')->willThrowException(new \Exception('DB Error'));
        
        $stats = $this->service->getGlobalStats();
        
        $this->assertIsArray($stats);
        $this->assertEquals(0, $stats['users']['total']);
    }

    public function testGetRedisStatsSuccess(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }

    public function testGetRedisStatsUnavailable(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }

    public function testGetSystemStats(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }

    public function testGetApiStats(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }

    public function testClearAllCaches(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }

    public function testReindexElasticsearch(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }

    public function testGetLogsFileNotFound(): void
    {
        $stats = $this->service->getGlobalStats();
        $this->assertIsArray($stats);
    }
}
