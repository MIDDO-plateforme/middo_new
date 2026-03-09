<?php

namespace App\Infrastructure\IA\Repository;

use App\Domain\IA\Entity\IaInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IaInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IaInteraction::class);
    }

    public function save(IaInteraction $interaction): void
    {
        $em = $this->getEntityManager();
        $em->persist($interaction);
        $em->flush();
    }
}
