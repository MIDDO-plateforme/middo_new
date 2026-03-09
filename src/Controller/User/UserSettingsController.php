<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/settings')]
class UserSettingsController extends AbstractController
{
    #[Route('/', name: 'user.settings.index')]
    public function index(): Response
    {
        return new Response('User Settings Controller OK');
    }
}
