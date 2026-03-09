<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class NotificationService
{
    private $entityManager;
    private $requestStack;
    private $notifications = [];

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->loadNotifications();
    }

    private function loadNotifications()
    {
        // Simuler des notifications (en production: depuis BDD)
        $this->notifications = [
            [
                'id' => 1,
                'type' => 'message',
                'title' => '💬 Nouveau message',
                'message' => 'Marie Curie vous a envoyé un message',
                'timestamp' => time() - 300,
                'read' => false,
                'userId' => 1,
            ],
            [
                'id' => 2,
                'type' => 'payment',
                'title' => ' Paiement reçu',
                'message' => 'Vous avez reçu 3,500€ de TechStart SAS',
                'timestamp' => time() - 3600,
                'read' => false,
                'userId' => 1,
            ],
            [
                'id' => 3,
                'type' => 'project',
                'title' => ' Projet accepté',
                'message' => 'Votre candidature pour "Audit Sécurité" a été acceptée',
                'timestamp' => time() - 7200,
                'read' => true,
                'userId' => 1,
            ],
            [
                'id' => 4,
                'type' => 'match',
                'title' => '🤝 Nouveau match IA',
                'message' => 'Mission parfaite trouvée: Développement Smart Contract (98% match)',
                'timestamp' => time() - 86400,
                'read' => true,
                'userId' => 1,
            ],
        ];
    }

    public function getAllNotifications(int $userId): array
    {
        return array_filter($this->notifications, function($notif) use ($userId) {
            return $notif['userId'] === $userId;
        });
    }

    public function getUnreadCount(int $userId): int
    {
        $userNotifications = $this->getAllNotifications($userId);
        return count(array_filter($userNotifications, function($notif) {
            return !$notif['read'];
        }));
    }

    public function createNotification(int $userId, string $type, string $title, string $message): array
    {
        $notification = [
            'id' => count($this->notifications) + 1,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'timestamp' => time(),
            'read' => false,
            'userId' => $userId,
        ];

        $this->notifications[] = $notification;

        return $notification;
    }

    public function markAsRead(int $notificationId): bool
    {
        foreach ($this->notifications as &$notif) {
            if ($notif['id'] === $notificationId) {
                $notif['read'] = true;
                return true;
            }
        }
        return false;
    }
}