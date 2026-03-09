<?php

namespace App\Application\Auth\Command;

class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
