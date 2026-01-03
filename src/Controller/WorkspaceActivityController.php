<?php

namespace App\Controller;

use App\Entity\Workspace;
use App\Entity\WorkspaceActivity;
use App\Repository\WorkspaceActivityRepository;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/workspace/{workspaceId}/activity')]
#[IsGranted('ROLE_USER')]
class WorkspaceActivityController extends AbstractController
{
    // Types d'activités supportés
    private const ACTIVITY_TYPES = [
        'workspace_created',
        'workspace_updated',
        'document_created',
        'document_updated',
        'document_deleted',
        'project_created',
        'project_updated',
        'project_deleted',
        'project_member_added',
        'project_member_removed',
        'task_created',
        'task_updated',
        'task_deleted',
        'task_status_changed',
        'task_time_logged',
        'collaborator_invited',
        'collaborator_revoked',
        'role_updated',
        'comment_created',
        'comment_updated',
        'comment_deleted'
    ];

    // Traductions des types d'activités
    private const ACTIVITY_LABELS = [
        'workspace_created' => '📁 Workspace créé',
        'workspace_updated' => '✏️ Workspace modifié',
        'document_created' => '📄 Document créé',
        'document_updated' => '📝 Document modifié',
        'document_deleted' => '🗑️ Document supprimé',
        'project_created' => '📊 Projet créé',
        'project_updated' => '🔄 Projet modifié',
        'project_deleted' => '❌ Projet supprimé',
        'project_member_added' => '👥 Membre ajouté au projet',
        'project_member_removed' => '👤 Membre retiré du projet',
        'task_created' => '✅ Tâche créée',
        'task_updated' => '🔧 Tâche modifiée',
        'task_deleted' => '🗑️ Tâche supprimée',
        'task_status_changed' => '🔄 Statut de tâche changé',
        'task_time_logged' => '⏱️ Temps enregistré',
        'collaborator_invited' => '📧 Collaborateur invité',
        'collaborator_revoked' => '🚫 Accès révoqué',
        'role_updated' => '🔐 Rôle modifié',
        'comment_created' => '💬 Commentaire ajouté',
        'comment_updated' => '✏️ Commentaire modifié',
        'comment_deleted' => '🗑️ Commentaire supprimé'
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorkspaceActivityRepository $activityRepository,
        private WorkspaceRepository $workspaceRepository
    ) {}

    /**
     * Affiche le feed d'activités du workspace
     */
    #[Route('/', name: 'app_workspace_activity_index', methods: ['GET'])]
    public function index(int $workspaceId, Request $request): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            throw $this->createNotFoundException('Workspace non trouvé');
        }

        // Filtres
        $actionType = $request->query->get('action_type');
        $entityType = $request->query->get('entity_type');
        $userId = $request->query->get('user_id');
        $dateFrom = $request->query->get('date_from');
        $dateTo = $request->query->get('date_to');
        $limit = $request->query->getInt('limit', 50);

        $queryBuilder = $this->activityRepository->createQueryBuilder('a')
            ->where('a.workspace = :workspace')
            ->setParameter('workspace', $workspace)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit);

        if ($actionType) {
            $queryBuilder->andWhere('a.actionType = :actionType')
                ->setParameter('actionType', $actionType);
        }

        if ($entityType) {
            $queryBuilder->andWhere('a.entityType = :entityType')
                ->setParameter('entityType', $entityType);
        }

        if ($userId) {
            $queryBuilder->andWhere('a.user = :userId')
                ->setParameter('userId', $userId);
        }

        if ($dateFrom) {
            $queryBuilder->andWhere('a.createdAt >= :dateFrom')
                ->setParameter('dateFrom', new \DateTimeImmutable($dateFrom));
        }

        if ($dateTo) {
            $queryBuilder->andWhere('a.createdAt <= :dateTo')
                ->setParameter('dateTo', new \DateTimeImmutable($dateTo));
        }

        $activities = $queryBuilder->getQuery()->getResult();

        return $this->render('workspace/activity/index.html.twig', [
            'workspace' => $workspace,
            'activities' => $activities,
            'activity_types' => self::ACTIVITY_TYPES,
            'activity_labels' => self::ACTIVITY_LABELS,
            'current_filters' => [
                'action_type' => $actionType,
                'entity_type' => $entityType,
                'user_id' => $userId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ]);
    }

    /**
     * Récupère les activités via API JSON
     */
    #[Route('/api', name: 'app_workspace_activity_api', methods: ['GET'])]
    public function api(int $workspaceId, Request $request): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            return $this->json(['error' => 'Workspace not found'], Response::HTTP_NOT_FOUND);
        }

        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);
        $actionType = $request->query->get('action_type');

        $queryBuilder = $this->activityRepository->createQueryBuilder('a')
            ->where('a.workspace = :workspace')
            ->setParameter('workspace', $workspace)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($actionType) {
            $queryBuilder->andWhere('a.actionType = :actionType')
                ->setParameter('actionType', $actionType);
        }

        $activities = $queryBuilder->getQuery()->getResult();

        $activitiesData = array_map(function(WorkspaceActivity $activity) {
            return [
                'id' => $activity->getId(),
                'action_type' => $activity->getActionType(),
                'action_label' => self::ACTIVITY_LABELS[$activity->getActionType()] ?? $activity->getActionType(),
                'entity_type' => $activity->getEntityType(),
                'entity_id' => $activity->getEntityId(),
                'user' => [
                    'id' => $activity->getUser()->getId(),
                    'email' => $activity->getUser()->getEmail(),
                    'username' => $activity->getUser()->getUsername()
                ],
                'metadata' => $activity->getMetadata(),
                'created_at' => $activity->getCreatedAt()->format('Y-m-d H:i:s'),
                'created_at_relative' => $this->getRelativeTime($activity->getCreatedAt())
            ];
        }, $activities);

        return $this->json([
            'success' => true,
            'activities' => $activitiesData,
            'total' => count($activitiesData),
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Récupère les statistiques d'activité
     */
    #[Route('/stats', name: 'app_workspace_activity_stats', methods: ['GET'])]
    public function stats(int $workspaceId, Request $request): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            return $this->json(['error' => 'Workspace not found'], Response::HTTP_NOT_FOUND);
        }

        $period = $request->query->get('period', '7days'); // 7days, 30days, 90days, all

        $dateFrom = match($period) {
            '7days' => new \DateTimeImmutable('-7 days'),
            '30days' => new \DateTimeImmutable('-30 days'),
            '90days' => new \DateTimeImmutable('-90 days'),
            default => null
        };

        $queryBuilder = $this->activityRepository->createQueryBuilder('a')
            ->where('a.workspace = :workspace')
            ->setParameter('workspace', $workspace);

        if ($dateFrom) {
            $queryBuilder->andWhere('a.createdAt >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom);
        }

        $activities = $queryBuilder->getQuery()->getResult();

        // Statistiques par type d'action
        $actionStats = [];
        foreach ($activities as $activity) {
            $actionType = $activity->getActionType();
            if (!isset($actionStats[$actionType])) {
                $actionStats[$actionType] = [
                    'count' => 0,
                    'label' => self::ACTIVITY_LABELS[$actionType] ?? $actionType
                ];
            }
            $actionStats[$actionType]['count']++;
        }

        // Statistiques par type d'entité
        $entityStats = [];
        foreach ($activities as $activity) {
            $entityType = $activity->getEntityType();
            if (!isset($entityStats[$entityType])) {
                $entityStats[$entityType] = 0;
            }
            $entityStats[$entityType]++;
        }

        // Statistiques par utilisateur
        $userStats = [];
        foreach ($activities as $activity) {
            $userId = $activity->getUser()->getId();
            if (!isset($userStats[$userId])) {
                $userStats[$userId] = [
                    'user' => $activity->getUser()->getEmail(),
                    'count' => 0
                ];
            }
            $userStats[$userId]['count']++;
        }

        // Activité par jour (derniers 7 jours)
        $dailyActivity = $this->calculateDailyActivity($activities, 7);

        // Utilisateurs les plus actifs (top 5)
        uasort($userStats, fn($a, $b) => $b['count'] <=> $a['count']);
        $topUsers = array_slice($userStats, 0, 5);

        return $this->json([
            'success' => true,
            'period' => $period,
            'total_activities' => count($activities),
            'action_stats' => $actionStats,
            'entity_stats' => $entityStats,
            'daily_activity' => $dailyActivity,
            'top_users' => array_values($topUsers)
        ]);
    }

    /**
     * Export des activités en CSV
     */
    #[Route('/export', name: 'app_workspace_activity_export', methods: ['GET'])]
    public function export(int $workspaceId): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            throw $this->createNotFoundException('Workspace non trouvé');
        }

        $activities = $this->activityRepository->findBy(
            ['workspace' => $workspace],
            ['createdAt' => 'DESC']
        );

        $csv = "ID,Type d'action,Type d'entité,ID entité,Utilisateur,Date,Métadonnées\n";

        foreach ($activities as $activity) {
            $csv .= sprintf(
                "%d,%s,%s,%d,%s,%s,\"%s\"\n",
                $activity->getId(),
                $activity->getActionType(),
                $activity->getEntityType(),
                $activity->getEntityId(),
                $activity->getUser()->getEmail(),
                $activity->getCreatedAt()->format('Y-m-d H:i:s'),
                json_encode($activity->getMetadata())
            );
        }

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf(
            'attachment; filename="workspace_%d_activities_%s.csv"',
            $workspaceId,
            (new \DateTime())->format('Y-m-d')
        ));

        return $response;
    }

    /**
     * Récupère les activités récentes pour le dashboard
     */
    #[Route('/recent', name: 'app_workspace_activity_recent', methods: ['GET'])]
    public function recent(int $workspaceId, Request $request): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            return $this->json(['error' => 'Workspace not found'], Response::HTTP_NOT_FOUND);
        }

        $limit = $request->query->getInt('limit', 10);

        $activities = $this->activityRepository->findBy(
            ['workspace' => $workspace],
            ['createdAt' => 'DESC'],
            $limit
        );

        $activitiesData = array_map(function(WorkspaceActivity $activity) {
            return [
                'id' => $activity->getId(),
                'action_type' => $activity->getActionType(),
                'action_label' => self::ACTIVITY_LABELS[$activity->getActionType()] ?? $activity->getActionType(),
                'user' => $activity->getUser()->getEmail(),
                'created_at' => $activity->getCreatedAt()->format('Y-m-d H:i:s'),
                'created_at_relative' => $this->getRelativeTime($activity->getCreatedAt()),
                'summary' => $this->generateActivitySummary($activity)
            ];
        }, $activities);

        return $this->json([
            'success' => true,
            'activities' => $activitiesData
        ]);
    }

    /**
     * Supprime les anciennes activités (nettoyage)
     */
    #[Route('/cleanup', name: 'app_workspace_activity_cleanup', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function cleanup(int $workspaceId, Request $request): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            return $this->json(['error' => 'Workspace not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $daysToKeep = $data['days_to_keep'] ?? 90;

        $dateThreshold = new \DateTimeImmutable("-{$daysToKeep} days");

        $deletedCount = $this->activityRepository->createQueryBuilder('a')
            ->delete()
            ->where('a.workspace = :workspace')
            ->andWhere('a.createdAt < :dateThreshold')
            ->setParameter('workspace', $workspace)
            ->setParameter('dateThreshold', $dateThreshold)
            ->getQuery()
            ->execute();

        return $this->json([
            'success' => true,
            'deleted_count' => $deletedCount,
            'days_kept' => $daysToKeep
        ]);
    }

    /**
     * Calcule l'activité quotidienne
     */
    private function calculateDailyActivity(array $activities, int $days): array
    {
        $dailyActivity = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = (new \DateTimeImmutable())->modify("-{$i} days")->format('Y-m-d');
            $dailyActivity[$date] = 0;
        }

        foreach ($activities as $activity) {
            $date = $activity->getCreatedAt()->format('Y-m-d');
            if (isset($dailyActivity[$date])) {
                $dailyActivity[$date]++;
            }
        }

        krsort($dailyActivity); // Tri chronologique
        return $dailyActivity;
    }

    /**
     * Génère un résumé textuel de l'activité
     */
    private function generateActivitySummary(WorkspaceActivity $activity): string
    {
        $metadata = $activity->getMetadata();
        $actionType = $activity->getActionType();

        return match($actionType) {
            'document_created' => sprintf(
                'a créé le document "%s"',
                $metadata['document_title'] ?? 'Sans titre'
            ),
            'project_created' => sprintf(
                'a créé le projet "%s"',
                $metadata['project_name'] ?? 'Sans nom'
            ),
            'task_created' => sprintf(
                'a créé la tâche "%s"',
                $metadata['task_title'] ?? 'Sans titre'
            ),
            'task_status_changed' => sprintf(
                'a changé le statut de "%s" à "%s"',
                $metadata['old_status'] ?? 'inconnu',
                $metadata['new_status'] ?? 'inconnu'
            ),
            'collaborator_invited' => sprintf(
                'a invité %s avec le rôle %s',
                $metadata['invited_user'] ?? 'un utilisateur',
                $metadata['role'] ?? 'MEMBER'
            ),
            default => 'a effectué une action'
        };
    }

    /**
     * Convertit une date en temps relatif
     */
    private function getRelativeTime(\DateTimeImmutable $date): string
    {
        $now = new \DateTimeImmutable();
        $diff = $now->getTimestamp() - $date->getTimestamp();

        if ($diff < 60) {
            return 'il y a quelques secondes';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return sprintf('il y a %d minute%s', $minutes, $minutes > 1 ? 's' : '');
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return sprintf('il y a %d heure%s', $hours, $hours > 1 ? 's' : '');
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return sprintf('il y a %d jour%s', $days, $days > 1 ? 's' : '');
        } else {
            return $date->format('d/m/Y à H:i');
        }
    }
}
