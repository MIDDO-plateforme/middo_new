<?php

namespace App\EventListener;

use App\Service\NotificationService;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class NotificationEventListener
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Exemple: déclencher une notification lors d'un événement
        // En production, écouter des événements custom (ProjectAccepted, PaymentReceived, etc.)
    }

    public function onProjectAccepted(int $userId, string $projectName)
    {
        $this->notificationService->createNotification(
            $userId,
            'project',
            ' Projet accepté',
            "Votre candidature pour \"{$projectName}\" a été acceptée"
        );
    }

    public function onPaymentReceived(int $userId, float $amount, string $from)
    {
        $this->notificationService->createNotification(
            $userId,
            'payment',
            ' Paiement reçu',
            "Vous avez reçu {$amount}€ de {$from}"
        );
    }

    public function onNewMessage(int $userId, string $fromUser)
    {
        $this->notificationService->createNotification(
            $userId,
            'message',
            ' Nouveau message',
            "{$fromUser} vous a envoyé un message"
        );
    }

    public function onMatchFound(int $userId, string $missionName, int $matchScore)
    {
        $this->notificationService->createNotification(
            $userId,
            'match',
            ' Nouveau match IA',
            "Mission parfaite trouvée: {$missionName} ({$matchScore}% match)"
        );
    }
}