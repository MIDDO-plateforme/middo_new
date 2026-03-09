<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;

/**
 * Service métier pour le Dashboard Analytics
 * Gère la logique d'agrégation de données, calculs de KPI et tendances
 */
class DashboardService
{
    private EntityManagerInterface $entityManager;
    private NotificationRepository $notificationRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationRepository $notificationRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Calcule les statistiques globales du dashboard
     */
    public function calculateGlobalStats(): array
    {
        $conn = $this->entityManager->getConnection();

        // Statistiques Projets
        $projectStats = $this->getProjectStats($conn);

        // Statistiques Tâches
        $taskStats = $this->getTaskStats($conn);

        // Statistiques Utilisateurs
        $userStats = [
            'total' => $this->userRepository->count([]),
            'active' => $this->getActiveUsersCount()
        ];

        // Statistiques Notifications
        $notificationStats = $this->getNotificationStats();

        return [
            'projects' => $projectStats,
            'tasks' => $taskStats,
            'users' => $userStats,
            'notifications' => $notificationStats,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Récupère les statistiques projets
     */
    private function getProjectStats($conn): array
    {
        try {
            $total = (int) $conn->fetchOne('SELECT COUNT(*) FROM project');
            $active = (int) $conn->fetchOne("SELECT COUNT(*) FROM project WHERE status = 'active'");
            $completed = (int) $conn->fetchOne("SELECT COUNT(*) FROM project WHERE status = 'completed'");

            return [
                'total' => $total,
                'active' => $active,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'active' => 0,
                'completed' => 0,
                'completion_rate' => 0,
                'note' => 'Table project pas encore créée'
            ];
        }
    }

    /**
     * Récupère les statistiques tâches
     */
    private function getTaskStats($conn): array
    {
        try {
            $total = (int) $conn->fetchOne('SELECT COUNT(*) FROM task');
            $completed = (int) $conn->fetchOne("SELECT COUNT(*) FROM task WHERE status = 'completed'");
            $pending = (int) $conn->fetchOne("SELECT COUNT(*) FROM task WHERE status = 'pending'");
            $inProgress = (int) $conn->fetchOne("SELECT COUNT(*) FROM task WHERE status = 'in_progress'");

            return [
                'total' => $total,
                'completed' => $completed,
                'pending' => $pending,
                'in_progress' => $inProgress,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'completed' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'completion_rate' => 0,
                'note' => 'Table task pas encore créée'
            ];
        }
    }

    /**
     * Récupère les statistiques notifications
     */
    private function getNotificationStats(): array
    {
        $total = $this->notificationRepository->count([]);
        $unread = $this->notificationRepository->count(['isRead' => false]);
        $read = $total - $unread;

        return [
            'total' => $total,
            'read' => $read,
            'unread' => $unread,
            'read_rate' => $total > 0 ? round(($read / $total) * 100, 1) : 0
        ];
    }

    /**
     * Compte les utilisateurs actifs (avec au moins 1 notification dans les 7 derniers jours)
     */
    private function getActiveUsersCount(): int
    {
        $conn = $this->entityManager->getConnection();

        try {
            return (int) $conn->fetchOne("
                SELECT COUNT(DISTINCT user_id)
                FROM notifications
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calcule les tendances (évolution sur 7 jours)
     */
    public function calculateTrends(int $days = 7): array
    {
        $conn = $this->entityManager->getConnection();

        $notificationsTrend = $conn->fetchAllAssociative("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM notifications
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", [$days]);

        return [
            'notifications' => $notificationsTrend,
            'period' => [
                'days' => $days,
                'start' => (new \DateTime("-{$days} days"))->format('Y-m-d'),
                'end' => (new \DateTime())->format('Y-m-d')
            ]
        ];
    }

    /**
     * Analyse les performances du système
     */
    public function analyzePerformance(): array
    {
        $notificationStats = $this->getNotificationStats();

        $readRate = $notificationStats['read_rate'];
        $engagementLevel = $readRate >= 70 ? 'excellent' : ($readRate >= 50 ? 'good' : 'needs_improvement');

        return [
            'notification_engagement' => [
                'level' => $engagementLevel,
                'read_rate' => $readRate,
                'recommendation' => $this->getEngagementRecommendation($engagementLevel)
            ],
            'system_health' => 'operational',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Retourne une recommandation basée sur le niveau d'engagement
     */
    private function getEngagementRecommendation(string $level): string
    {
        return match($level) {
            'excellent' => 'Excellent taux d\'engagement ! Maintenez la qualité des notifications.',
            'good' => 'Bon engagement. Considérez optimiser le contenu des notifications.',
            'needs_improvement' => 'Amélioration nécessaire. Revoyez la pertinence et le timing des notifications.',
            default => 'Continuez de monitorer les performances.'
        };
    }

    /**
     * Génère un rapport complet du dashboard
     */
    public function generateReport(): array
    {
        return [
            'stats' => $this->calculateGlobalStats(),
            'trends' => $this->calculateTrends(7),
            'performance' => $this->analyzePerformance(),
            'generated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
    }
}
