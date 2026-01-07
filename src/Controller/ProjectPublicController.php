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
        $fakeProjects = [
            [
                'id' => 1,
                'title' => 'Test MIDDO Design Premium',
                'description' => 'Validation of MIDDO design system with signature color #f4a261',
                'createdAt' => new \DateTime(),
            ],
            [
                'id' => 2,
                'title' => 'Template Premium Animations',
                'description' => 'Verification of 11 premium CSS animations integrated',
                'createdAt' => new \DateTime(),
            ],
            [
                'id' => 3,
                'title' => 'Design System MIDDO',
                'description' => 'Complete UI test with sentiment gauges',
                'createdAt' => new \DateTime(),
            ],
        ];

        return $this->render('project/index.html.twig', [
            'projects' => $fakeProjects,
        ]);
    }
}