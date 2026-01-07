<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectPublicController extends AbstractController
{
    #[Route('/projets-public', name: 'app_project_public', methods: ['GET'])]
    public function index(): Response
    {
        // Version de test simple - juste afficher du HTML brut
        return new Response('<h1 style="color: #f4a261; font-family: Poppins;"> TEST MIDDO - Route fonctionne !</h1>');
    }
}
