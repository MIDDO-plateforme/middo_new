<?php

namespace App\Entity;

use App\Repository\PartnerConnectorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnerConnectorRepository::class)]
class PartnerConnector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PartnerApp $app = null;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $config = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApp(): ?PartnerApp
    {
        return $this->app;
    }

    public function setApp(PartnerApp $app): self
    {
        $this->app = $app;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function setConfig(?array $config): self
    {
        $this->config = $config;
        return $this;
    }
}
