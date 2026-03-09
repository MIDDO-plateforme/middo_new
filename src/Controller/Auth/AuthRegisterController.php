<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth/register')]
class AuthRegisterController extends AbstractController
{
    #[Route('/', name: 'auth.register.index')]
    public function index(): Response
    {
        return $this->render('auth/register/index.html.twig');
    }
}
