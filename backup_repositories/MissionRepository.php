<?php

namespace App\Repository;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function save(Mission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOpenMissions(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.status = :status')
            ->setParameter('status', 'open')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySkills(array $skills): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.status = :status')
            ->setParameter('status', 'open');

        foreach ($skills as $index => $skill) {
            $qb->orWhere('JSON_CONTAINS(m.skills, :skill' . $index . ') = 1')
               ->setParameter('skill' . $index, json_encode($skill));
        }

        return $qb->orderBy('m.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }
}