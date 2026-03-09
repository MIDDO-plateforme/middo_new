<?php

namespace App\Domain\User\ValueObject;

class PasswordHash
{
    public function __construct(
        private string $hash
    ) {}

    public function value(): string
    {
        return $this->hash;
    }
}
