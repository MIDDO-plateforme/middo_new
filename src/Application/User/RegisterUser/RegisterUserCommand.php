<?php

namespace App\Application\User\RegisterUser;

class RegisterUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $firstname = null,
        public readonly ?string $lastname = null,
        public readonly ?string $locale = 'fr_FR',
        public readonly ?string $timezone = 'Europe/Paris'
    ) {}
}
