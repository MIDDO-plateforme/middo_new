<?php

namespace App\Message;

class NotificationMassMessage
{
    private array $userIds;
    private string $message;
    private string $type;

    public function __construct(array $userIds, string $message, string $type = 'info')
    {
        $this->userIds = $userIds;
        $this->message = $message;
        $this->type = $type;
    }

    public function getUserIds(): array
    {
        return $this->userIds;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }
}