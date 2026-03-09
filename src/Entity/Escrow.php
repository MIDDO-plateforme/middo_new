<?php

namespace App\Entity;

use App\Repository\EscrowRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\User\UserDoctrineEntity;

#[ORM\Entity(repositoryClass: EscrowRepository::class)]
class Escrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserDoctrineEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserDoctrineEntity $user = null;

    #[ORM\Column]
    private float $amount;

    #[ORM\Column(length: 50)]
    private string $status = 'locked';

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

    public function setUser(UserDoctrineEntity $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
