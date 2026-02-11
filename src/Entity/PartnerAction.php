<?php

namespace App\Entity;

use App\Repository\PartnerActionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\PartnerConnector;

#[ORM\Entity(repositoryClass: PartnerActionRepository::class)]
class PartnerAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PartnerConnector::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PartnerConnector $partnerConnector = null;

    #[ORM\Column(length: 100)]
    private string $actionType;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $parameters = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $result = [];

    #[ORM\Column(length: 20)]
    private string $status = 'pending';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $executedAt = null;

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

    public function getPartnerConnector(): ?PartnerConnector
    {
        return $this->partnerConnector;
    }

    public function setPartnerConnector(PartnerConnector $partnerConnector): static
    {
        $this->partnerConnector = $partnerConnector;
        return $this;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function setActionType(string $actionType): static
    {
        $this->actionType = $actionType;
        return $this;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function setParameters(?array $parameters): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function setResult(?array $result): static
    {
        $this->result = $result;
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

    public function getExecutedAt(): ?\DateTimeImmutable
    {
        return $this->executedAt;
    }

    public function setExecutedAt(?\DateTimeImmutable $executedAt): static
    {
        $this->executedAt = $executedAt;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
