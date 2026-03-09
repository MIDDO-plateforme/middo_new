<?php

namespace App\AI\Step;

final class StepResult
{
    public function __construct(
        private readonly string $name,
        private readonly mixed $data,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
