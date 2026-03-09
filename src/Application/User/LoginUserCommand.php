<?php

namespace App\Application\User;

class LoginUserCommand
{
    public string $email;
    public string $password;

    public function __construct(
        string $email,
        string $password
    ) {
        $this->email = $email;
        $this->password = $password;
    }
}
