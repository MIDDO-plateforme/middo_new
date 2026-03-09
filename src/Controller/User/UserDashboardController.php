<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/dashboard')]
class UserDashboardController extends AbstractController
{
    #[Route('/', name: 'user.dashboard.index')]
    public function index(): Response
    {
        return new Response('User Dashboard Controller OK');
    }
}
