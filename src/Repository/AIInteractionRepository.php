<?php

namespace App\Repository;

use App\Entity\AIInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AIInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AIInteraction::class);
    }

    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('ai')
            ->andWhere('ai.user = :id')
            ->setParameter('id', $userId)
            ->orderBy('ai.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
