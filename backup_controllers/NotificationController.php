<?php

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    #[Route('/api/notifications', name: 'api_notifications_list', methods: ['GET'])]
    public function getNotifications(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId', 1);
        $notifications = $this->notificationService->getAllNotifications($userId);

        return $this->json([
            'success' => true,
            'notifications' => array_values($notifications),
            'count' => count($notifications),
        ]);
    }

    #[Route('/api/notifications/{id}/read', name: 'api_notifications_mark_read', methods: ['POST'])]
    public function markAsRead(int $id): JsonResponse
    {
        $success = $this->notificationService->markAsRead($id);

        return $this->json([
            'success' => $success,
            'message' => $success ? 'Notification marquée comme lue' : 'Notification introuvable',
        ]);
    }

    #[Route('/api/notifications/unread/count', name: 'api_notifications_unread_count', methods: ['GET'])]
    public function getUnreadCount(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId', 1);
        $count = $this->notificationService->getUnreadCount($userId);

        return $this->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    #[Route('/api/notifications/create', name: 'api_notifications_create', methods: ['POST'])]
    public function createNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['userId'] ?? 1;
        $type = $data['type'] ?? 'system';
        $title = $data['title'] ?? 'Nouvelle notification';
        $message = $data['message'] ?? '';

        $notification = $this->notificationService->createNotification($userId, $type, $title, $message);

        return $this->json([
            'success' => true,
            'notification' => $notification,
            'message' => 'Notification créée avec succès',
        ]);
    }
}