<?php

namespace App\Infrastructure\IA\Repository;

use App\Domain\IA\Entity\IaMemory;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IaMemoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IaMemory::class);
    }

    public function get(User $user, string $key): ?IaMemory
    {
        return $this->findOneBy(['user' => $user, 'key' => $key]);
    }

    public function set(User $user, string $key, string $value): void
    {
        $memory = $this->get($user, $key) ?? new IaMemory();
        $memory->setUser($user)->setKey($key)->setValue($value)->refreshTimestamp();

        $em = $this->getEntityManager();
        $em->persist($memory);
        $em->flush();
    }
}
