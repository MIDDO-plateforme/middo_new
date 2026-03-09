<?php

namespace App\Repository;

use App\Entity\PartnerApp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PartnerAppRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerApp::class);
    }

    public function findEnabled(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.enabled = true')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
