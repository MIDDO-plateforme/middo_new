<?php

namespace App\Service\Notification;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationSender
{
    public function __construct(
        private EntityManagerInterface $em,
        private NotificationRepository $notificationRepository
    ) {}

    public function send(int $userId, string $title, ?string $message = null): Notification
    {
        $notification = new Notification();
        $notification->setUser($this->em->getReference('App\Entity\User', $userId));
        $notification->setTitle($title);
        $notification->setMessage($message);

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }
}
