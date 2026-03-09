<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Trouve tous les projets d'un utilisateur (créateur ou membre)
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.members', 'm')
            ->where('p.creator = :user')
            ->orWhere('m = :user')
            ->setParameter('user', $user)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche par nom
     */
    public function searchByName(string $query, User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.members', 'm')
            ->where('p.name LIKE :query')
            ->andWhere('p.creator = :user OR m = :user')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('user', $user)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Filtre par statut
     */
    public function findByStatus(string $status, User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.members', 'm')
            ->where('p.status = :status')
            ->andWhere('p.creator = :user OR m = :user')
            ->setParameter('status', $status)
            ->setParameter('user', $user)
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}