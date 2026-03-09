<?php

namespace App\Repository;

use App\Entity\PartnerConnector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PartnerConnectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerConnector::class);
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('pc')
            ->andWhere('pc.type = :type')
            ->setParameter('type', $type)
            ->orderBy('pc.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
