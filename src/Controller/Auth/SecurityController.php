<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/security')]
class SecurityController extends AbstractController
{
    #[Route('/', name: 'auth.security.index')]
    public function index(): Response
    {
        return $this->render('auth/security/index.html.twig');
    }
}
