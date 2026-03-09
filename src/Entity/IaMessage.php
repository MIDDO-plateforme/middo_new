<?php

namespace App\Entity;

use App\Repository\IaMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IaMessageRepository::class)]
class IaMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?IaConversation $conversation = null;

    #[ORM\Column(length: 20)]
    private ?string $sender = null; // 'user' ou 'ia'

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getConversation(): ?IaConversation { return $this->conversation; }
    public function setConversation(?IaConversation $conversation): self { $this->conversation = $conversation; return $this; }

    public function getSender(): ?string { return $this->sender; }
    public function setSender(string $sender): self { $this->sender = $sender; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
}
