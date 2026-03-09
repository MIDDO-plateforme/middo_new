<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\WorkspaceRepository;
use App\Repository\WorkspaceProjectRepository;
use App\Repository\WorkspaceTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AdminDashboardService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly WorkspaceProjectRepository $projectRepository,
        private readonly WorkspaceTaskRepository $taskRepository,
        private readonly LoggerInterface $logger
    ) {}

    public function getGlobalStats(): array
    {
        try {
            return [
                'users' => [
                    'total' => $this->userRepository->count([]),
                    'active' => $this->userRepository->countActiveUsers(),
                    'new_this_month' => $this->userRepository->countNewUsersThisMonth()
                ],
                'workspaces' => [
                    'total' => $this->workspaceRepository->count([]),
                    'active' => $this->workspaceRepository->countActiveWorkspaces()
                ],
                'projects' => [
                    'total' => $this->projectRepository->count([]),
                    'in_progress' => $this->projectRepository->countByStatus('in_progress'),
                    'completed' => $this->projectRepository->countByStatus('completed')
                ],
                'tasks' => [
                    'total' => $this->taskRepository->count([]),
                    'todo' => $this->taskRepository->countByStatus('todo'),
                    'in_progress' => $this->taskRepository->countByStatus('in_progress'),
                    'done' => $this->taskRepository->countByStatus('done')
                ]
            ];

        } catch (\Exception $e) {
            $this->logger->error('Error fetching global stats', [
                'error' => $e->getMessage()
            ]);

            return [
                'users' => ['total' => 0, 'active' => 0, 'new_this_month' => 0],
                'workspaces' => ['total' => 0, 'active' => 0],
                'projects' => ['total' => 0, 'in_progress' => 0, 'completed' => 0],
                'tasks' => ['total' => 0, 'todo' => 0, 'in_progress' => 0, 'done' => 0]
            ];
        }
    }

    public function getRecentActivity(int $limit = 20): array
    {
        try {
            // Récupérer les activités récentes
            $recentProjects = $this->projectRepository->findBy(
                [],
                ['createdAt' => 'DESC'],
                $limit
            );

            $recentTasks = $this->taskRepository->findBy(
                [],
                ['createdAt' => 'DESC'],
                $limit
            );

            return [
                'projects' => array_map(fn($p) => [
                    'id' => $p->getId(),
                    'name' => $p->getName(),
                    'status' => $p->getStatus(),
                    'created_at' => $p->getCreatedAt()->format('Y-m-d H:i:s')
                ], $recentProjects),
                'tasks' => array_map(fn($t) => [
                    'id' => $t->getId(),
                    'title' => $t->getTitle(),
                    'status' => $t->getStatus(),
                    'created_at' => $t->getCreatedAt()->format('Y-m-d H:i:s')
                ], $recentTasks)
            ];

        } catch (\Exception $e) {
            $this->logger->error('Error fetching recent activity', [
                'error' => $e->getMessage()
            ]);

            return [
                'projects' => [],
                'tasks' => []
            ];
        }
    }

    public function getPerformanceMetrics(): array
    {
        try {
            return [
                'average_project_completion_time' => $this->projectRepository->getAverageCompletionTime(),
                'average_task_completion_time' => $this->taskRepository->getAverageCompletionTime(),
                'user_engagement_rate' => $this->calculateUserEngagementRate(),
                'workspace_growth_rate' => $this->calculateWorkspaceGrowthRate()
            ];

        } catch (\Exception $e) {
            $this->logger->error('Error fetching performance metrics', [
                'error' => $e->getMessage()
            ]);

            return [
                'average_project_completion_time' => 0,
                'average_task_completion_time' => 0,
                'user_engagement_rate' => 0,
                'workspace_growth_rate' => 0
            ];
        }
    }

    private function calculateUserEngagementRate(): float
    {
        $totalUsers = $this->userRepository->count([]);
        
        if ($totalUsers === 0) {
            return 0.0;
        }

        $activeUsers = $this->userRepository->countActiveUsers();
        
        return round(($activeUsers / $totalUsers) * 100, 2);
    }

    private function calculateWorkspaceGrowthRate(): float
    {
        $currentMonth = $this->workspaceRepository->countCreatedInCurrentMonth();
        $lastMonth = $this->workspaceRepository->countCreatedInLastMonth();

        if ($lastMonth === 0) {
            return $currentMonth > 0 ? 100.0 : 0.0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2);
    }
}

