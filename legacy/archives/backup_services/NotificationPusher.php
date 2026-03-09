<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service pour créer et pousser des notifications en temps réel
 */
class NotificationPusher
{
    private EntityManagerInterface $entityManager;
    private NotificationRepository $notificationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationRepository $notificationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Créer et envoyer une notification à un utilisateur
     */
    public function push(User $user, string $title, string $message, string $type = 'info', ?array $data = null): Notification
    {
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setType($type);
        $notification->setCreatedAt(new \DateTime());
        
        if ($data) {
            $notification->setData($data);
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    /**
     * Créer une notification de test
     */
    public function pushTestNotification(User $user): Notification
    {
        $types = ['success', 'info', 'warning', 'error'];
        $messages = [
            'success' => 'Tâche terminée avec succès !',
            'info' => 'Nouvelle mise à jour disponible',
            'warning' => 'Action requise sur votre projet',
            'error' => 'Échec de synchronisation'
        ];

        $type = $types[array_rand($types)];
        
        return $this->push(
            $user,
            'Notification Test',
            $messages[$type],
            $type,
            ['test' => true, 'timestamp' => time()]
        );
    }

    /**
     * Notifications système pour tous les utilisateurs
     */
    public function pushToAll(string $title, string $message, string $type = 'info'): int
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $count = 0;

        foreach ($users as $user) {
            $this->push($user, $title, $message, $type);
            $count++;
        }

        return $count;
    }

    /**
     * Notification pour un nouveau projet
     */
    public function notifyNewProject(User $user, string $projectName): Notification
    {
        return $this->push(
            $user,
            'Nouveau projet créé',
            "Le projet '{$projectName}' a été créé avec succès",
            'success',
            ['type' => 'project_created', 'project_name' => $projectName]
        );
    }

    /**
     * Notification pour une tâche assignée
     */
    public function notifyTaskAssigned(User $user, string $taskTitle): Notification
    {
        return $this->push(
            $user,
            'Nouvelle tâche assignée',
            "Vous avez été assigné à la tâche: {$taskTitle}",
            'info',
            ['type' => 'task_assigned', 'task_title' => $taskTitle]
        );
    }

    /**
     * Notification pour une tâche terminée
     */
    public function notifyTaskCompleted(User $user, string $taskTitle): Notification
    {
        return $this->push(
            $user,
            'Tâche terminée',
            "La tâche '{$taskTitle}' a été marquée comme terminée",
            'success',
            ['type' => 'task_completed', 'task_title' => $taskTitle]
        );
    }

    /**
     * Notification d'alerte
     */
    public function notifyAlert(User $user, string $message): Notification
    {
        return $this->push(
            $user,
            'Alerte Système',
            $message,
            'warning',
            ['type' => 'alert']
        );
    }
}
