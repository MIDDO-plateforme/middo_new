<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntrepreneurController extends AbstractController
{
    #[Route('/entrepreneur', name: 'app_entrepreneur')]
    public function index(): Response
    {
        return $this->render('entrepreneur/index.html.twig', [
            'controller_name' => 'EntrepreneurController',
        ]);
    }
}
