<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Récupère toutes les conversations d'un utilisateur
     * (liste des personnes avec qui il a échangé)
     */
    public function findConversations(User $user): array
    {
        $qb = $this->createQueryBuilder('m');
        
        return $qb
            ->select('DISTINCT IDENTITY(m.sender) as userId')
            ->where('m.recipient = :user')
            ->setParameter('user', $user)
            ->union(
                $qb->select('DISTINCT IDENTITY(m.recipient) as userId')
                   ->where('m.sender = :user')
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les messages entre deux utilisateurs
     */
    public function findConversationBetween(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.recipient = :user2)')
            ->orWhere('(m.sender = :user2 AND m.recipient = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les messages non lus d'un utilisateur
     */
    public function countUnreadMessages(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.recipient = :user')
            ->andWhere('m.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Marque tous les messages d'une conversation comme lus
     */
    public function markConversationAsRead(User $recipient, User $sender): void
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', 'true')
            ->where('m.recipient = :recipient')
            ->andWhere('m.sender = :sender')
            ->andWhere('m.isRead = false')
            ->setParameter('recipient', $recipient)
            ->setParameter('sender', $sender)
            ->getQuery()
            ->execute();
    }
}
