<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/notifications')]
class AdminNotificationController extends AbstractController
{
    #[Route('/', name: 'admin.notifications.index')]
    public function index(): Response
    {
        return $this->render('admin/notifications/index.html.twig');
    }
}
