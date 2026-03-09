<?php

namespace App\Repository;

use App\Entity\Escrow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EscrowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Escrow::class);
    }

    public function findLockedByUser(int $userId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :id')
            ->andWhere('e.status = :status')
            ->setParameter('id', $userId)
            ->setParameter('status', 'locked')
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
