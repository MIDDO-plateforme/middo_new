<?php

namespace App\Service\Notification;

class InAppNotificationService
{
    public function __construct(
        private NotificationSender $sender
    ) {}

    public function notify(int $userId, string $title, ?string $message = null): void
    {
        $this->sender->send($userId, $title, $message);
    }
}
