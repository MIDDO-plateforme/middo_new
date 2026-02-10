<?php

namespace App\Entity;

use App\Repository\IaPromptTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IaPromptTemplateRepository::class)]
class IaPromptTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    private ?string $prompt = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $active = true;

    public function getId(): ?int { return $id ?? null; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getPrompt(): ?string { return $this->prompt; }
    public function setPrompt(string $prompt): self { $this->prompt = $prompt; return $this; }

    public function getCategory(): ?string { return $this->category; }
    public function setCategory(?string $category): self { $this->category = $category; return $this; }

    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): self { $this->active = $active; return $this; }
}
