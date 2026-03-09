<?php

namespace App\Infrastructure\User\Security;

use App\Domain\User\Service\PasswordHasherInterface;

class PasswordHasherBcrypt implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }
}
