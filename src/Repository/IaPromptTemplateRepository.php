<?php

namespace App\Repository;

use App\Entity\IaPromptTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IaPromptTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IaPromptTemplate::class);
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.title LIKE :q OR t.category LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
