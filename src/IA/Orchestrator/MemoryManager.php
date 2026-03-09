<?php

namespace App\IA\Orchestrator;

class MemoryManager
{
    private array $declarative = [];
    private array $vector = [];
    private array $procedural = [];

    public function storeFlux(string $flux): void
    {
        $this->declarative[] = [
            'type' => 'flux',
            'value' => $flux,
            'time' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];
    }

    public function storePlan(array $plan): void
    {
        $this->procedural[] = [
            'type' => 'plan',
            'value' => $plan,
            'time' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];
    }

    public function storeSafety(array $safety): void
    {
        $this->procedural[] = [
            'type' => 'safety',
            'value' => $safety,
            'time' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];
    }

    public function storeExecution(array $execution): void
    {
        $this->procedural[] = [
            'type' => 'execution',
            'value' => $execution,
            'time' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];
    }

    public function getMemory(): array
    {
        return [
            'declarative' => $this->declarative,
            'vector' => $this->vector,
            'procedural' => $this->procedural,
        ];
    }
}
