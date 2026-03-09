<?php

namespace App\Infrastructure\Auth\Security;

class JwtSecret
{
    public function __construct(
        public readonly string $value
    ) {}
}
