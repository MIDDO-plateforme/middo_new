<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/2fa')]
class TwoFactorController extends AbstractController
{
    #[Route('/', name: 'auth.2fa.index')]
    public function index(): Response
    {
        return $this->render('auth/2fa/index.html.twig');
    }
}
