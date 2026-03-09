<?php

namespace App\Application\Notification;

use App\Domain\Notification\Entity\UserNotification;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function notify(User $user, string $title, string $message): void
    {
        $notification = new UserNotification(
            id: uuid_create(UUID_TYPE_RANDOM),
            user: $user,
            title: $title,
            message: $message
        );

        $this->em->persist($notification);
        $this->em->flush();
    }
}

