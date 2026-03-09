<?php

namespace App\Domain\User\ValueObject;

class Email
{
    public function __construct(
        private string $value
    ) {}

    public function value(): string
    {
        return $this->value;
    }
}
