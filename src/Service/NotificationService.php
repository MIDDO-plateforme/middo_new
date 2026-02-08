<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class NotificationService
{
    // Types de notifications
    public const TYPE_PROJECT_NEW = 'project_new';
    public const TYPE_PROJECT_UPDATE = 'project_update';
    public const TYPE_MESSAGE_NEW = 'message_new';
    public const TYPE_MATCH_NEW = 'match_new';
    public const TYPE_MATCH_ACCEPTED = 'match_accepted';
    public const TYPE_SYSTEM = 'system';
    public const TYPE_ALERT = 'alert';
    public const TYPE_SUCCESS = 'success';

    // Priorités
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    // Quotas par plan (nombre par jour)
    private const QUOTAS = [
        'free' => 10,
        'premium' => PHP_INT_MAX,
        'business' => PHP_INT_MAX,
        'enterprise' => PHP_INT_MAX,
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private NotificationRepository $notificationRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    /**
     * Créer une notification
     */
    public function create(
        User $user,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?string $icon = null,
        string $priority = self::PRIORITY_NORMAL
    ): ?Notification {
        // Vérifier le quota
        if (!$this->canSendNotification($user)) {
            // Quota dépassé - on ne crée pas la notification
            return null;
        }

        $notification = new Notification();
        $notification->setUser($user);
        $notification->setType($type);
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setData($data);
        $notification->setActionUrl($actionUrl);
        $notification->setActionLabel($actionLabel);
        $notification->setIcon($icon);
        $notification->setPriority($priority);

        $this->em->persist($notification);
        $this->em->flush();

        // Dispatcher un event pour interconnexion
        $event = new GenericEvent($notification, [
            'user' => $user,
            'type' => $type,
            'title' => $title,
        ]);
        $this->eventDispatcher->dispatch($event, 'notification.created');

        return $notification;
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Notification $notification): void
    {
        if (!$notification->isRead()) {
            $notification->setIsRead(true);
            $this->em->flush();

            // Event
            $event = new GenericEvent($notification);
            $this->eventDispatcher->dispatch($event, 'notification.read');
        }
    }

    /**
     * Marquer plusieurs notifications comme lues
     */
    public function markMultipleAsRead(array $notifications): void
    {
        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }
        $this->em->flush();
    }

    /**
     * Marquer toutes les notifications comme lues pour un utilisateur
     */
    public function markAllAsRead(User $user): int
    {
        return $this->notificationRepository->markAllAsReadForUser($user);
    }

    /**
     * Obtenir les notifications d'un utilisateur
     */
    public function getUserNotifications(User $user, int $limit = 20, int $offset = 0): array
    {
        return $this->notificationRepository->findByUser($user, $limit, $offset);
    }

    /**
     * Obtenir les notifications non lues d'un utilisateur
     */
    public function getUnreadNotifications(User $user, int $limit = 50): array
    {
        return $this->notificationRepository->findUnreadByUser($user, $limit);
    }

    /**
     * Compter les notifications non lues
     */
    public function countUnread(User $user): int
    {
        return $this->notificationRepository->countUnreadByUser($user);
    }

    /**
     * Supprimer une notification
     */
    public function delete(Notification $notification): void
    {
        $this->em->remove($notification);
        $this->em->flush();
    }

    /**
     * Nettoyer les anciennes notifications
     */
    public function cleanup(int $daysOld = 30): int
    {
        return $this->notificationRepository->deleteOldNotifications($daysOld);
    }

    /**
     * Vérifier si l'utilisateur peut recevoir une notification (quota)
     */
    public function canSendNotification(User $user): bool
    {
        // Récupérer le plan de l'utilisateur
        // TODO: Quand entité User aura subscriptionPlan, utiliser ça
        // Pour l'instant, on considère tous les users comme 'free'
        $userPlan = 'free'; // $user->getSubscriptionPlan() ?? 'free';
        
        $quota = self::QUOTAS[$userPlan] ?? self::QUOTAS['free'];

        // Si quota illimité
        if ($quota === PHP_INT_MAX) {
            return true;
        }

        // Compter les notifications créées aujourd'hui
        $today = new \DateTime('today');
        $count = $this->em->createQueryBuilder()
            ->select('COUNT(n.id)')
            ->from(Notification::class, 'n')
            ->where('n.user = :user')
            ->andWhere('n.createdAt >= :today')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->getQuery()
            ->getSingleScalarResult();

        return $count < $quota;
    }

    /**
     * Obtenir le quota restant aujourd'hui
     */
    public function getRemainingQuota(User $user): int
    {
        $userPlan = 'free'; // $user->getSubscriptionPlan() ?? 'free';
        $quota = self::QUOTAS[$userPlan] ?? self::QUOTAS['free'];

        if ($quota === PHP_INT_MAX) {
            return PHP_INT_MAX;
        }

        $today = new \DateTime('today');
        $used = $this->em->createQueryBuilder()
            ->select('COUNT(n.id)')
            ->from(Notification::class, 'n')
            ->where('n.user = :user')
            ->andWhere('n.createdAt >= :today')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->getQuery()
            ->getSingleScalarResult();

        return max(0, $quota - $used);
    }

    /**
     * Obtenir les statistiques des notifications
     */
    public function getStats(User $user): array
    {
        return $this->notificationRepository->getStatsByUser($user);
    }

    /**
     * Méthodes raccourcies pour types courants
     */
    public function notifyNewProject(User $user, string $projectTitle, string $projectUrl): ?Notification
    {
        return $this->create(
            user: $user,
            type: self::TYPE_PROJECT_NEW,
            title: 'Nouveau projet',
            message: "Le projet \"{$projectTitle}\" a été créé.",
            actionUrl: $projectUrl,
            actionLabel: 'Voir le projet',
            icon: '🚀'
        );
    }

    public function notifyProjectUpdate(User $user, string $projectTitle, string $projectUrl): ?Notification
    {
        return $this->create(
            user: $user,
            type: self::TYPE_PROJECT_UPDATE,
            title: 'Projet mis à jour',
            message: "Le projet \"{$projectTitle}\" a été modifié.",
            actionUrl: $projectUrl,
            actionLabel: 'Voir les modifications',
            icon: '📝'
        );
    }

    public function notifyNewMessage(User $user, string $senderName, string $conversationUrl): ?Notification
    {
        return $this->create(
            user: $user,
            type: self::TYPE_MESSAGE_NEW,
            title: 'Nouveau message',
            message: "{$senderName} vous a envoyé un message.",
            actionUrl: $conversationUrl,
            actionLabel: 'Lire le message',
            icon: '💬',
            priority: self::PRIORITY_HIGH
        );
    }

    public function notifyNewMatch(User $user, string $matchTitle, string $matchUrl): ?Notification
    {
        return $this->create(
            user: $user,
            type: self::TYPE_MATCH_NEW,
            title: 'Nouveau matching',
            message: "Vous avez un nouveau match : {$matchTitle}",
            actionUrl: $matchUrl,
            actionLabel: 'Voir le match',
            icon: '🎯',
            priority: self::PRIORITY_HIGH
        );
    }

    public function notifySystem(User $user, string $title, string $message, ?string $actionUrl = null): ?Notification
    {
        return $this->create(
            user: $user,
            type: self::TYPE_SYSTEM,
            title: $title,
            message: $message,
            actionUrl: $actionUrl,
            actionLabel: $actionUrl ? 'En savoir plus' : null,
            icon: '⚙️'
        );
    }
}
