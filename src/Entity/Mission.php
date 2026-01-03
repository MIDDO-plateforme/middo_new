<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\Table(name: 'missions')]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $budget = null;

    #[ORM\Column(length: 100)]
    private ?string $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $company = null;

    #[ORM\Column(type: 'json')]
    private array $skills = [];

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 50)]
    private ?string $urgency = 'medium';

    #[ORM\Column(length: 50)]
    private ?string $status = 'open';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters et Setters...
    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function getBudget(): ?float { return (float) $this->budget; }
    public function setBudget(float $budget): static { $this->budget = (string) $budget; return $this; }
    public function getDuration(): ?string { return $this->duration; }
    public function setDuration(string $duration): static { $this->duration = $duration; return $this; }
    public function getCompany(): ?string { return $this->company; }
    public function setCompany(string $company): static { $this->company = $company; return $this; }
    public function getSkills(): array { return $this->skills; }
    public function setSkills(array $skills): static { $this->skills = $skills; return $this; }
    public function getLocation(): ?string { return $this->location; }
    public function setLocation(string $location): static { $this->location = $location; return $this; }
    public function getUrgency(): ?string { return $this->urgency; }
    public function setUrgency(string $urgency): static { $this->urgency = $urgency; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
}