<?php

namespace App\Domain\IA\Entity;

use App\Domain\User\Entity\User;
use App\Infrastructure\IA\Repository\IaInteractionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IaInteractionRepository::class)]
class IaInteraction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\Column(type: 'text')]
    private string $prompt;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $answer = null;

    #[ORM\Column(length: 50)]
    private string $provider;

    #[ORM\Column(type: 'boolean')]
    private bool $success;

    #[ORM\Column(type: 'float')]
    private float $durationMs;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getPrompt(): string { return $this->prompt; }
    public function setPrompt(string $prompt): self { $this->prompt = $prompt; return $this; }

    public function getAnswer(): ?string { return $this->answer; }
    public function setAnswer(?string $answer): self { $this->answer = $answer; return $this; }

    public function getProvider(): string { return $this->provider; }
    public function setProvider(string $provider): self { $this->provider = $provider; return $this; }

    public function isSuccess(): bool { return $this->success; }
    public function setSuccess(bool $success): self { $this->success = $success; return $this; }

    public function getDurationMs(): float { return $this->durationMs; }
    public function setDurationMs(float $durationMs): self { $this->durationMs = $durationMs; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
