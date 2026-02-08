<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Centre de notifications - Page principale
     */
    #[Route('', name: 'app_notifications_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $notifications = $this->notificationService->getUserNotifications($user, $limit, $offset);
        $stats = $this->notificationService->getStats($user);
        $unreadCount = $this->notificationService->countUnread($user);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
            'stats' => $stats,
            'unreadCount' => $unreadCount,
            'currentPage' => $page,
            'hasMore' => count($notifications) === $limit,
        ]);
    }

    /**
     * API - Obtenir le nombre de notifications non lues
     */
    #[Route('/api/count', name: 'app_notifications_count', methods: ['GET'])]
    public function count(): JsonResponse
    {
        $user = $this->getUser();
        $count = $this->notificationService->countUnread($user);

        return $this->json([
            'count' => $count,
            'hasUnread' => $count > 0,
        ]);
    }

    /**
     * API - Obtenir les notifications récentes
     */
    #[Route('/api/recent', name: 'app_notifications_recent', methods: ['GET'])]
    public function recent(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $limit = min(50, $request->query->getInt('limit', 10));
        
        $notifications = $this->notificationService->getUnreadNotifications($user, $limit);

        $data = array_map(function(Notification $n) {
            return [
                'id' => $n->getId(),
                'type' => $n->getType(),
                'title' => $n->getTitle(),
                'message' => $n->getMessage(),
                'icon' => $n->getDefaultIcon(),
                'actionUrl' => $n->getActionUrl(),
                'actionLabel' => $n->getActionLabel(),
                'isRead' => $n->isRead(),
                'timeAgo' => $n->getTimeAgo(),
                'createdAt' => $n->getCreatedAt()->format('Y-m-d H:i:s'),
                'priority' => $n->getPriority(),
                'color' => $n->getTypeColor(),
            ];
        }, $notifications);

        return $this->json([
            'notifications' => $data,
            'count' => count($data),
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    #[Route('/{id}/read', name: 'app_notifications_mark_read', methods: ['POST'])]
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }

        $this->notificationService->markAsRead($notification);

        return $this->json([
            'success' => true,
            'message' => 'Notification marquée comme lue',
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    #[Route('/read-all', name: 'app_notifications_read_all', methods: ['POST'])]
    public function markAllAsRead(): JsonResponse
    {
        $user = $this->getUser();
        $count = $this->notificationService->markAllAsRead($user);

        return $this->json([
            'success' => true,
            'message' => "{$count} notification(s) marquée(s) comme lue(s)",
            'count' => $count,
        ]);
    }

    /**
     * Supprimer une notification
     */
    #[Route('/{id}/delete', name: 'app_notifications_delete', methods: ['DELETE', 'POST'])]
    public function delete(Notification $notification): JsonResponse
    {
        // Vérifier que la notification appartient à l'utilisateur
        if ($notification->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }

        $this->notificationService->delete($notification);

        return $this->json([
            'success' => true,
            'message' => 'Notification supprimée',
        ]);
    }

    /**
     * Obtenir les statistiques
     */
    #[Route('/api/stats', name: 'app_notifications_stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $user = $this->getUser();
        $stats = $this->notificationService->getStats($user);
        $remaining = $this->notificationService->getRemainingQuota($user);

        return $this->json([
            'stats' => $stats,
            'quota' => [
                'remaining' => $remaining,
                'isUnlimited' => $remaining === PHP_INT_MAX,
            ],
        ]);
    }

    /**
     * Dropdown notifications (pour la navbar)
     */
    #[Route('/dropdown', name: 'app_notifications_dropdown', methods: ['GET'])]
    public function dropdown(): Response
    {
        $user = $this->getUser();
        $notifications = $this->notificationService->getUnreadNotifications($user, 5);
        $count = $this->notificationService->countUnread($user);

        return $this->render('notification/_dropdown.html.twig', [
            'notifications' => $notifications,
            'count' => $count,
        ]);
    }
}
