<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/login')]
class AuthLoginController extends AbstractController
{
    #[Route('/', name: 'auth.login.index')]
    public function index(): Response
    {
        return $this->render('auth/login/index.html.twig');
    }
}
