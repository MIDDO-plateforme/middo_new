<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectPublicController extends AbstractController
{
    #[Route('/projets-public', name: 'app_project_public', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        try {
            $projects = $projectRepository->findAll();
            
            if (empty($projects)) {
                return new Response(
                    '<h1 style="color: #f4a261; font-family: Poppins; padding: 40px;">Base de donnees vide - Aucun projet trouve</h1>',
                    Response::HTTP_OK
                );
            }
            
            return $this->render('project/index.html.twig', [
                'projects' => $projects,
            ]);
            
        } catch (\Exception $e) {
            return new Response(
                '<div style="font-family: Poppins; padding: 40px;">' .
                '<h1 style="color: #f4a261;">MIDDO Platform - Mode Debug</h1>' .
                '<p><strong>Erreur detectee:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>' .
                '<p><strong>Type:</strong> ' . get_class($e) . '</p>' .
                '<p><strong>Solution:</strong> La base de donnees Render est probablement en spindown (gratuit). Reessaie dans 30 secondes.</p>' .
                '</div>',
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
    }
}