<?php

namespace App\Service\Notification;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private NotificationRepository $notificationRepository
    ) {}

    public function create(
        User $user,
        string $type,
        string $title,
        string $message
    ): Notification {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setTitle($title);
        $notification->setMessage($message);

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }

    public function getUserNotifications(User $user, int $limit = 20, int $offset = 0): array
    {
        return $this->notificationRepository->findByUser($user, $limit, $offset);
    }

    public function getUnreadNotifications(User $user, int $limit = 50): array
    {
        return $this->notificationRepository->findUnreadByUser($user, $limit);
    }

    public function countUnread(User $user): int
    {
        return $this->notificationRepository->countUnreadByUser($user);
    }

    public function markAsRead(Notification $notification): void
    {
        if (!$notification->isRead()) {
            $notification->setIsRead(true);
            $this->em->flush();
        }
    }

    public function delete(Notification $notification): void
    {
        $this->em->remove($notification);
        $this->em->flush();
    }
}
