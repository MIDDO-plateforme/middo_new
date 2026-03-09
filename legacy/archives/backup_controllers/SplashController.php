<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SplashController extends AbstractController
{
    #[Route('/splash', name: 'app_splash')]
    public function index(): Response
    {
        return $this->render('splash/index.html.twig');
    }

    #[Route('/intro', name: 'app_intro')]
    public function intro(): Response
    {
        return $this->render('splash/intro.html.twig');
    }
}
