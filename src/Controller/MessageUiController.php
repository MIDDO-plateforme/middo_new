<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageUiController extends AbstractController
{
    #[Route('/messages', name: 'app_messages', methods: ['GET'])]
    public function inbox(): Response
    {
        // TODO: Récupérer les conversations de l'utilisateur
        return $this->render('messages/inbox.html.twig');
    }
}