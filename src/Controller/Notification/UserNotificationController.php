<?php

namespace App\Controller\Notification;

use App\Domain\Notification\Entity\UserNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
final class UserNotificationController extends AbstractController
{
    #[Route('', name: 'user_notifications')]
    public function index(EntityManagerInterface $em): Response
    {
        $notifications = $em->getRepository(UserNotification::class)
            ->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
