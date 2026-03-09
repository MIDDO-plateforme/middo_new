<?php

namespace App\Application\Auth\Handler;

use App\Application\Auth\Command\RegisterUserCommand;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;

class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $hasher
    ) {}

    public function __invoke(RegisterUserCommand $command)
    {
        // 🔥 Conversion string → Value Object
        $email = new Email($command->email);

        // 🔥 Recherche avec Email VO
        $existing = $this->users->findByEmail($email);

        if ($existing) {
            throw new \RuntimeException('Email already registered');
        }

        // 🔥 Hash du mot de passe
        $passwordHash = $this->hasher->hash($command->password);

        // 🔥 Création d’un User DDD propre
        $user = User::register(
            id: UserId::generate(),
            email: $email,
            passwordHash: $passwordHash
        );

        // 🔥 Persistance
        $this->users->save($user);

        return $user->id;
    }
}
