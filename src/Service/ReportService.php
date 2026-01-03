<?php

namespace App\Service;

use App\Repository\TaskRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ReportService
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private NotificationRepository $notificationRepository;
    private UserRepository $userRepository;
    private ?LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository,
        NotificationRepository $notificationRepository,
        UserRepository $userRepository,
        ?LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    /**
     * Générer les statistiques globales pour rapports
     */
    public function getGlobalStats(): array
    {
        try {
            // Stats Tasks
            $totalTasks = $this->taskRepository->count([]);
            $completedTasks = $this->taskRepository->count(['status' => 'completed']);
            $pendingTasks = $this->taskRepository->count(['status' => 'pending']);
            $inProgressTasks = $this->taskRepository->count(['status' => 'in_progress']);

            // Stats Notifications - CORRIGÉ : compter manuellement avec isRead()
            $allNotifications = $this->notificationRepository->findAll();
            $totalNotifications = count($allNotifications);
            $readNotifications = 0;
            foreach ($allNotifications as $notif) {
                if ($notif->isRead()) {
                    $readNotifications++;
                }
            }
            $unreadNotifications = $totalNotifications - $readNotifications;

            // Stats Users
            $totalUsers = $this->userRepository->count([]);

            // Calculs KPI
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
            $readRate = $totalNotifications > 0 ? round(($readNotifications / $totalNotifications) * 100, 2) : 0;
            $avgTasksPerUser = $totalUsers > 0 ? round($totalTasks / $totalUsers, 2) : 0;

            return [
                'tasks' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                    'pending' => $pendingTasks,
                    'in_progress' => $inProgressTasks,
                    'completion_rate' => $completionRate,
                ],
                'notifications' => [
                    'total' => $totalNotifications,
                    'read' => $readNotifications,
                    'unread' => $unreadNotifications,
                    'read_rate' => $readRate,
                ],
                'users' => [
                    'total' => $totalUsers,
                    'avg_tasks_per_user' => $avgTasksPerUser,
                ],
                'kpi' => [
                    'overall_completion' => $completionRate,
                    'engagement_rate' => $readRate,
                    'productivity_score' => round(($completionRate + $readRate) / 2, 2),
                ],
                'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            $this->logger?->error('Report stats generation failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtenir les données pour rapport projet
     */
    public function getProjectData(int $projectId): array
    {
        try {
            $tasks = $this->taskRepository->findAll();

            $projectTasks = array_map(function($task) {
                return [
                    'id' => $task->getId(),
                    'title' => $task->getTitle(),
                    'status' => $task->getStatus(),
                    'priority' => $task->getPriority(),
                    'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
                    'completed_at' => $task->getCompletedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $tasks);

            return [
                'project_id' => $projectId,
                'project_name' => "Projet MIDDO #{$projectId}",
                'tasks' => $projectTasks,
                'tasks_count' => count($projectTasks),
                'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            $this->logger?->error('Project report generation failed', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtenir les tâches pour export CSV
     */
    public function getTasksForExport(array $filters = []): array
    {
        try {
            $tasks = $this->taskRepository->findAll();

            return array_map(function($task) {
                return [
                    'ID' => $task->getId(),
                    'Titre' => $task->getTitle(),
                    'Description' => $task->getDescription() ?? '',
                    'Statut' => $task->getStatus(),
                    'Priorite' => $task->getPriority(),
                    'Date_Creation' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
                    'Date_Echeance' => $task->getDueDate()?->format('Y-m-d H:i:s') ?? '',
                    'Date_Completion' => $task->getCompletedAt()?->format('Y-m-d H:i:s') ?? '',
                    'Tags' => implode(', ', $task->getTags() ?? []),
                ];
            }, $tasks);
        } catch (\Exception $e) {
            $this->logger?->error('Tasks export failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtenir les notifications pour export CSV
     */
    public function getNotificationsForExport(array $filters = []): array
    {
        try {
            $notifications = $this->notificationRepository->findAll();

            return array_map(function($notification) {
                return [
                    'ID' => $notification->getId(),
                    'Type' => $notification->getType(),
                    'Message' => $notification->getMessage(),
                    'Lu' => $notification->isRead() ? 'Oui' : 'Non',
                    'Date_Creation' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                    'Date_Lecture' => $notification->getReadAt()?->format('Y-m-d H:i:s') ?? '',
                    'User_ID' => $notification->getUser()?->getId() ?? 'N/A',
                ];
            }, $notifications);
        } catch (\Exception $e) {
            $this->logger?->error('Notifications export failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtenir l'activité des utilisateurs
     */
    public function getUsersActivity(): array
    {
        try {
            $users = $this->userRepository->findAll();

            return array_map(function($user) {
                $userId = $user->getId();
                
                // Compter les notifications de l'utilisateur
                $userNotifications = $this->notificationRepository->count(['user' => $user]);

                return [
                    'user_id' => $userId,
                    'email' => $user->getEmail(),
                    'tasks_count' => 0,
                    'notifications_count' => $userNotifications,
                    'last_activity' => (new \DateTime())->format('Y-m-d H:i:s'),
                ];
            }, $users);
        } catch (\Exception $e) {
            $this->logger?->error('Users activity report failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Générer un rapport personnalisé
     */
    public function generateCustomReport(array $params): array
    {
        try {
            $reportType = $params['type'] ?? 'summary';
            $dateFrom = $params['date_from'] ?? null;
            $dateTo = $params['date_to'] ?? null;

            $report = [
                'type' => $reportType,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                ],
                'data' => [],
                'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            switch ($reportType) {
                case 'summary':
                    $report['data'] = $this->getGlobalStats();
                    break;
                case 'tasks':
                    $report['data'] = $this->getTasksForExport();
                    break;
                case 'notifications':
                    $report['data'] = $this->getNotificationsForExport();
                    break;
                case 'users':
                    $report['data'] = $this->getUsersActivity();
                    break;
                default:
                    $report['data'] = $this->getGlobalStats();
            }

            return $report;
        } catch (\Exception $e) {
            $this->logger?->error('Custom report generation failed', [
                'params' => $params,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Générer des insights IA (recommandations)
     */
    public function generateInsights(): array
    {
        try {
            $stats = $this->getGlobalStats();
            $insights = [];

            // Insight 1 : Taux de complétion
            if ($stats['tasks']['completion_rate'] < 50) {
                $insights[] = [
                    'type' => 'warning',
                    'category' => 'productivity',
                    'message' => "Taux de complétion faible ({$stats['tasks']['completion_rate']}%). Recommandation : Prioriser les tâches urgentes.",
                    'priority' => 'high',
                ];
            } else {
                $insights[] = [
                    'type' => 'success',
                    'category' => 'productivity',
                    'message' => "Excellent taux de complétion ({$stats['tasks']['completion_rate']}%) ! Continuez ainsi.",
                    'priority' => 'low',
                ];
            }

            // Insight 2 : Engagement notifications
            if ($stats['notifications']['read_rate'] < 70) {
                $insights[] = [
                    'type' => 'info',
                    'category' => 'engagement',
                    'message' => "Taux de lecture des notifications bas ({$stats['notifications']['read_rate']}%). Suggestion : Améliorer la pertinence des notifications.",
                    'priority' => 'medium',
                ];
            }

            // Insight 3 : Répartition des tâches
            if ($stats['tasks']['pending'] > $stats['tasks']['in_progress']) {
                $insights[] = [
                    'type' => 'warning',
                    'category' => 'workflow',
                    'message' => "Plus de tâches en attente ({$stats['tasks']['pending']}) qu'en cours ({$stats['tasks']['in_progress']}). Recommandation : Commencer plus de tâches.",
                    'priority' => 'medium',
                ];
            }

            return [
                'insights' => $insights,
                'total_insights' => count($insights),
                'generated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            $this->logger?->error('Insights generation failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}