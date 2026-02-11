<?php

namespace App\Entity;

use App\Repository\PartnerConnectorRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\PartnerApp;
use App\Entity\User;

#[ORM\Entity(repositoryClass: PartnerConnectorRepository::class)]
class PartnerConnector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: PartnerApp::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PartnerApp $partnerApp = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $settings = [];

    #[ORM\Column(length: 20)]
    private string $status = 'disconnected';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getPartnerApp(): ?PartnerApp
    {
        return $this->partnerApp;
    }

    public function setPartnerApp(PartnerApp $partnerApp): static
    {
        $this->partnerApp = $partnerApp;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): static
    {
        $this->settings = $settings;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
