<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectBuilderController extends AbstractController
{
    #[Route('/projects', name: 'app_projects')]
    public function index(): Response
    {
        return $this->render('domains/projects/builder/index.html.twig', [
            'controller_name' => 'ProjectBuilderController',
        ]);
    }
}