<?php

namespace App\Application\User\LoginUser;

class LoginUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $deviceId = null
    ) {}
}
