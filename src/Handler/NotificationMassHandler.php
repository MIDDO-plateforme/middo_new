<?php

namespace App\Handler;

use App\Message\NotificationMassMessage;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationMassHandler
{
    private EntityManagerInterface $entityManager;
    private NotificationRepository $notificationRepository;
    private ?LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationRepository $notificationRepository,
        ?LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
        $this->logger = $logger;
    }

    public function __invoke(NotificationMassMessage $message): void
    {
        $userIds = $message->getUserIds();
        $messageText = $message->getMessage();
        $type = $message->getType();

        $this->logger?->info('Processing mass notification', [
            'user_count' => count($userIds),
            'type' => $type,
        ]);

        try {
            foreach ($userIds as $userId) {
                // Logique d'envoi de notification
                // À implémenter selon votre système de notifications
                $this->logger?->info('Notification sent', [
                    'user_id' => $userId,
                    'message' => $messageText,
                ]);
            }

            $this->logger?->info('Mass notification completed', [
                'total_sent' => count($userIds),
            ]);
        } catch (\Exception $e) {
            $this->logger?->error('Mass notification failed', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}