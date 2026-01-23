<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 3 utilisateurs de test classiques
        $testUsers = [
            ['email' => 'test1@middo.app', 'password' => 'password123', 'roles' => ['ROLE_USER']],
            ['email' => 'test2@middo.app', 'password' => 'password123', 'roles' => ['ROLE_USER']],
            ['email' => 'test3@middo.app', 'password' => 'password123', 'roles' => ['ROLE_USER']],
        ];

        // 3 développeurs
        $devUsers = [
            ['email' => 'dev.alpha@middo.app', 'password' => 'dev123', 'roles' => ['ROLE_USER', 'ROLE_DEVELOPER']],
            ['email' => 'dev.beta@middo.app', 'password' => 'dev123', 'roles' => ['ROLE_USER', 'ROLE_DEVELOPER']],
            ['email' => 'dev.gamma@middo.app', 'password' => 'dev123', 'roles' => ['ROLE_USER', 'ROLE_DEVELOPER']],
        ];

        $allUsers = array_merge($testUsers, $devUsers);

        foreach ($allUsers as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);

            // Hasher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            // Définir les rôles
            $user->setRoles($userData['roles']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

