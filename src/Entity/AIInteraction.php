<?php

namespace App\Entity;

use App\Repository\AIInteractionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\User\UserDoctrineEntity;

#[ORM\Entity(repositoryClass: AIInteractionRepository::class)]
class AIInteraction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserDoctrineEntity::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?UserDoctrineEntity $user = null;

    #[ORM\Column(type: 'text')]
    private string $prompt;

    #[ORM\Column(type: 'text')]
    private string $response;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserDoctrineEntity
    {
        return $this->user;
    }

    public function setUser(?UserDoctrineEntity $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): self
    {
        $this->prompt = $prompt;
        return $this;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
