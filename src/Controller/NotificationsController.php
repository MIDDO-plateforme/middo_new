<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationsController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function index(): Response
    {
        // Données temporaires (mock) en attendant la vraie base
        $notifications = [
            [
                'id' => 1,
                'title' => 'Nouveau document analysé',
                'message' => 'Votre fichier "Contrat de travail.pdf" a été analysé avec succès.',
                'createdAt' => new \DateTime('-1 hour'),
            ],
            [
                'id' => 2,
                'title' => 'Mise à jour système',
                'message' => 'MIDDO OS a été mis à jour avec les dernières optimisations.',
                'createdAt' => new \DateTime('-5 hours'),
            ],
            [
                'id' => 3,
                'title' => 'Rappel',
                'message' => 'Vous avez un document en attente d’analyse.',
                'createdAt' => new \DateTime('-1 day'),
            ],
        ];

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
