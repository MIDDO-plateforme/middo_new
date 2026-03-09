<?php

namespace App\Repository;

use App\Entity\AIInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour les interactions IA
 * 
 * Méthodes utiles pour SESSION 13+ :
 * - Historique des conversations par utilisateur
 * - Analytics des types d'interactions
 * - Temps de réponse moyen par provider
 */
class AIInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AIInteraction::class);
    }

    /**
     * Trouve les dernières interactions d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre max de résultats
     * @return AIInteraction[]
     */
    public function findRecentByUser(int $userId, int $limit = 10): array
    {
        return $this->createQueryBuilder('ai')
            ->andWhere('ai.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('ai.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les interactions par type
     * 
     * @return array
     */
    public function countByType(): array
    {
        return $this->createQueryBuilder('ai')
            ->select('ai.interactionType, COUNT(ai.id) as total')
            ->groupBy('ai.interactionType')
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule le temps de réponse moyen par provider
     * 
     * @return array
     */
    public function averageResponseTimeByProvider(): array
    {
        return $this->createQueryBuilder('ai')
            ->select('ai.aiProvider, AVG(ai.responseTimeMs) as avgTime')
            ->where('ai.responseTimeMs IS NOT NULL')
            ->groupBy('ai.aiProvider')
            ->getQuery()
            ->getResult();
    }
}