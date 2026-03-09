<?php

namespace App\Controller\Project;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project/api')]
class ProjectApiController extends AbstractController
{
    #[Route('/', name: 'project.api.index')]
    public function index(): Response
    {
        return new Response('Project API Controller OK');
    }
}
