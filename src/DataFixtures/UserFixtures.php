<?php

namespace App\DataFixtures;

use App\Infrastructure\User\UserDoctrineEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new UserDoctrineEntity(
            id: 'user-1',
            email: 'test@example.com',
            password: password_hash('password123', PASSWORD_BCRYPT),
            iaSettings: []
        );

        $manager->persist($user);
        $manager->flush();
    }
}
