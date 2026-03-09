<?php

namespace App\Application\Auth\Command;

class LoginUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $deviceId = null
    ) {}
}
