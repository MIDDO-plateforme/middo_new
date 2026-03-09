<?php

namespace App\Controller\IA;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/project')]
final class ProjectPageController extends AbstractController
{
    #[Route('', name: 'ia_project_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ia/project.html.twig');
    }
}
