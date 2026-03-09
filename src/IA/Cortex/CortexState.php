<?php

namespace App\IA\Cortex;

class CortexState
{
    private array $state = [
        'user' => [],
        'flux' => [],
        'analyses' => [],
        'predictions' => [],
        'simulations' => [],
        'pipelines' => [],
        'meta' => [],
    ];

    public function getAll(): array
    {
        return $this->state;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->state[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->state[$key] = $value;
    }

    public function merge(string $key, array $value): void
    {
        $current = $this->state[$key] ?? [];
        if (!is_array($current)) {
            $current = [];
        }
        $this->state[$key] = array_merge($current, $value);
    }

    public function append(string $key, mixed $value): void
    {
        if (!isset($this->state[$key]) || !is_array($this->state[$key])) {
            $this->state[$key] = [];
        }
        $this->state[$key][] = $value;
    }
}
