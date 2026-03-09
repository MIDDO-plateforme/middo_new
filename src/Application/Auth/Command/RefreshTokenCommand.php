<?php

namespace App\Application\Auth\Command;

class RefreshTokenCommand
{
    public function __construct(
        public string $refreshToken,
        public string $deviceId
    ) {}
}
