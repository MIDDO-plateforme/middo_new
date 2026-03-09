<?php

namespace App\IA\Cortex\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cortex_memory')]
class CortexMemory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $memoryKey;

    #[ORM\Column(type: 'json')]
    private array $memoryValue = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMemoryKey(): string
    {
        return $this->memoryKey;
    }

    public function setMemoryKey(string $key): self
    {
        $this->memoryKey = $key;
        return $this;
    }

    public function getMemoryValue(): array
    {
        return $this->memoryValue;
    }

    public function setMemoryValue(array $value): self
    {
        $this->memoryValue = $value;
        return $this;
    }
}
