<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/logs')]
class AuthLogsController extends AbstractController
{
    #[Route('/', name: 'auth.logs.index')]
    public function index(): Response
    {
        return $this->render('auth/logs/index.html.twig');
    }
}
