<?php

namespace App\Controller\Notification;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification/api')]
class NotificationApiController extends AbstractController
{
    #[Route('/', name: 'notification.api.index')]
    public function index(): Response
    {
        return new Response('Notification API Controller OK');
    }
}
