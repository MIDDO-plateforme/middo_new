<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/session')]
class SessionController extends AbstractController
{
    #[Route('/', name: 'auth.session.index')]
    public function index(): Response
    {
        return $this->render('auth/session/index.html.twig');
    }
}
