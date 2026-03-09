<?php

namespace App\Infrastructure\Auth\Security;

use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Ici on suppose que ton UserRepository a une méthode findByEmail()
        $user = $this->users->findByEmail($identifier);

        if (!$user) {
            throw new UserNotFoundException("User not found.");
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        // Symfony exige que refreshUser recharge l'utilisateur depuis son identifiant unique
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === \App\Domain\User\Entity\User::class;
    }
}
