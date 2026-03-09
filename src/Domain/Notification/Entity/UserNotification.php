<?php

namespace App\Domain\Notification\Entity;

use App\Domain\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_notification")]
class UserNotification
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\Column(type: "text")]
    private string $message;

    #[ORM\Column(type: "boolean")]
    private bool $isRead = false;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    public function __construct(string $id, User $user, string $title, string $message)
    {
        $this->id = $id;
        $this->user = $user;
        $this->title = $title;
        $this->message = $message;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function id(): string { return $this->id; }
    public function user(): User { return $this->user; }
    public function title(): string { return $this->title; }
    public function message(): string { return $this->message; }
    public function createdAt(): \DateTimeInterface { return $this->createdAt; }
    public function isRead(): bool { return $this->isRead; }

    public function markAsRead(): void
    {
        $this->isRead = true;
    }
}
