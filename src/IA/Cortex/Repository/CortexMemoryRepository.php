<?php

namespace App\IA\Cortex\Repository;

use App\IA\Cortex\Entity\CortexMemory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CortexMemoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CortexMemory::class);
    }

    public function findByKey(string $key): ?CortexMemory
    {
        return $this->findOneBy(['memoryKey' => $key]);
    }

    public function save(CortexMemory $memory): void
    {
        $em = $this->getEntityManager();
        $em->persist($memory);
        $em->flush();
    }
}
