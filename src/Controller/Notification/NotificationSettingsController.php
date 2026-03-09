<?php

namespace App\Controller\Notification;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification/settings')]
class NotificationSettingsController extends AbstractController
{
    #[Route('/', name: 'notification.settings.index')]
    public function index(): Response
    {
        return new Response('Notification Settings Controller OK');
    }
}
