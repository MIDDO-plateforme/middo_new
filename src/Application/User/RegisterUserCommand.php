<?php

namespace App\Application\User;

class RegisterUserCommand
{
    public string $email;
    public string $password;
    public array $iaSettings;

    public function __construct(
        string $email,
        string $password,
        array $iaSettings = []
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->iaSettings = $iaSettings;
    }
}
