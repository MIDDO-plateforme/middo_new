<?php

namespace App\Application\User;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\PasswordHash;
use App\Infrastructure\User\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class RegisterUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function handle(RegisterUserCommand $command): User
    {
        $email = new Email($command->email);

        $hashedPassword = $this->passwordHasher->hashPassword(
            new class implements \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface {
                public function getPassword(): ?string { return null; }
            },
            $command->password
        );

        $user = new User(
            id: Uuid::v4()->toRfc4122(),
            email: $email,
            passwordHash: new PasswordHash($hashedPassword),
            iaSettings: $command->iaSettings
        );

        $this->userRepository->save($user);

        return $user;
    }
}
