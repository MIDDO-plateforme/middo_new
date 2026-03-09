<?php

namespace App\Domain\Auth\ValueObject;

class TokenPair
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken
    ) {}
}
