<?php

namespace App\Repository;

use App\Entity\PartnerAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PartnerActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerAction::class);
    }

    public function findByAction(string $action): array
    {
        return $this->createQueryBuilder('pa')
            ->andWhere('pa.action = :action')
            ->setParameter('action', $action)
            ->orderBy('pa.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
