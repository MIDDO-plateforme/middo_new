<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Trouver toutes les tâches avec filtres optionnels
     */
    public function findWithFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.assignedTo', 'u')
            ->addSelect('u');

        if (isset($filters['status'])) {
            $qb->andWhere('t.status = :status')
               ->setParameter('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $qb->andWhere('t.priority = :priority')
               ->setParameter('priority', $filters['priority']);
        }

        if (isset($filters['assignedTo'])) {
            $qb->andWhere('t.assignedTo = :assignedTo')
               ->setParameter('assignedTo', $filters['assignedTo']);
        }

        $orderBy = $filters['orderBy'] ?? 'createdAt';
        $order = $filters['order'] ?? 'DESC';
        $qb->orderBy('t.' . $orderBy, $order);

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouver les tâches d'un utilisateur
     */
    public function findByUser(User $user, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.assignedTo = :user')
            ->setParameter('user', $user)
            ->orderBy('t.dueDate', 'ASC');

        if ($status) {
            $qb->andWhere('t.status = :status')
               ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouver les tâches en retard
     */
    public function findOverdue(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.dueDate < :now')
            ->andWhere('t.status != :completed')
            ->setParameter('now', new \DateTime())
            ->setParameter('completed', 'completed')
            ->orderBy('t.dueDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Statistiques des tâches
     */
    public function getStatistics(): array
    {
        $total = $this->count([]);
        $completed = $this->count(['status' => 'completed']);
        $pending = $this->count(['status' => 'pending']);
        $inProgress = $this->count(['status' => 'in_progress']);

        $overdue = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.dueDate < :now')
            ->andWhere('t.status != :completed')
            ->setParameter('now', new \DateTime())
            ->setParameter('completed', 'completed')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'in_progress' => $inProgress,
            'overdue' => (int) $overdue,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
        ];
    }

    /**
     * Tâches par statut
     */
    public function countByStatus(): array
    {
        $results = $this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as count')
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();

        $stats = [
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];

        foreach ($results as $result) {
            $stats[$result['status']] = (int) $result['count'];
        }

        return $stats;
    }

    /**
     * Tâches par priorité
     */
    public function countByPriority(): array
    {
        $results = $this->createQueryBuilder('t')
            ->select('t.priority, COUNT(t.id) as count')
            ->groupBy('t.priority')
            ->getQuery()
            ->getResult();

        $stats = [
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'urgent' => 0
        ];

        foreach ($results as $result) {
            $stats[$result['priority']] = (int) $result['count'];
        }

        return $stats;
    }

    /**
     * Tâches créées par jour (7 derniers jours)
     */
    public function getCreatedByDay(int $days = 7): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count
            FROM task
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ";

        return $conn->fetchAllAssociative($sql, ['days' => $days]);
    }
}
