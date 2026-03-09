<?php

namespace App\IA\Cortex;

class CortexEngine
{
    private array $longTermMemory = [];
    private array $globalState = [
        'mood' => 'neutral',
        'energy' => 100,
        'focus' => 'general',
        'lastAction' => null,
    ];

    public function remember(string $key, mixed $value): void
    {
        $this->longTermMemory[$key] = $value;
    }

    public function recall(string $key): mixed
    {
        return $this->longTermMemory[$key] ?? null;
    }

    public function getLongTermMemory(): array
    {
        return $this->longTermMemory;
    }

    public function updateState(string $key, mixed $value): void
    {
        $this->globalState[$key] = $value;
    }

    public function getState(string $key): mixed
    {
        return $this->globalState[$key] ?? null;
    }

    public function getGlobalState(): array
    {
        return $this->globalState;
    }

    public function autoRegulate(): void
    {
        $this->globalState['energy'] = max(0, $this->globalState['energy'] - 1);

        if ($this->globalState['lastAction']) {
            $this->globalState['focus'] = $this->globalState['lastAction'];
        }

        if ($this->globalState['energy'] > 70) {
            $this->globalState['mood'] = 'focused';
        } elseif ($this->globalState['energy'] > 30) {
            $this->globalState['mood'] = 'neutral';
        } else {
            $this->globalState['mood'] = 'tired';
        }
    }

    public function setLastAction(string $action): void
    {
        $this->globalState['lastAction'] = $action;
    }
}
