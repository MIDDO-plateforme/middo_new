<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On récupère l'utilisateur #1 comme owner
        /** @var User|null $owner */
        $owner = $manager->getRepository(User::class)->find(1);

        if (!$owner) {
            throw new \RuntimeException('User #1 must exist before loading ProjectFixtures.');
        }

        // Projet 1
        $p1 = new Project();
        $p1->setName('Projet Alpha');
        $p1->setDescription('Premier projet de démonstration.');
        $p1->setStatus('active');
        $p1->setOwner($owner);
        $p1->addMember($owner);

        // Projet 2
        $p2 = new Project();
        $p2->setName('Projet Beta');
        $p2->setDescription('Projet en cours de développement.');
        $p2->setStatus('draft');
        $p2->setOwner($owner);

        // Projet 3
        $p3 = new Project();
        $p3->setName('Projet Gamma');
        $p3->setDescription('Projet archivé pour référence.');
        $p3->setStatus('archived');
        $p3->setOwner($owner);

        $manager->persist($p1);
        $manager->persist($p2);
        $manager->persist($p3);

        $manager->flush();
    }
}
