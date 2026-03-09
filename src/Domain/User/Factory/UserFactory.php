<?php

namespace App\Domain\User\Factory;

use App\Domain\User\Entity\User;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\IaSettings;
use Symfony\Component\Uid\Uuid;

class UserFactory
{
    public function __construct(
        private PasswordHasherInterface $passwordHasher
    ) {}

    public function create(Email $email, string $plainPassword): User
    {
        $id = Uuid::v4()->toRfc4122();
        $hashed = $this->passwordHasher->hash($plainPassword);

        return new User(
            id: $id,
            email: $email,
            password: $hashed,
            iaSettings: new IaSettings()
        );
    }
}
