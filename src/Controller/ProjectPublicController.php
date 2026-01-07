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
        // Retry logic pour DB spindown
        $maxRetries = 3;
        $retryDelay = 2; // secondes
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $projects = $projectRepository->findAll();
                
                if (empty($projects)) {
                    return new Response(
                        '<div style="font-family: Poppins; padding: 40px; color: #264653;">' .
                        '<h1 style="color: #f4a261;"> Base de donnees vide</h1>' .
                        '<p>Aucun projet trouve dans la base de donnees.</p>' .
                        '<p><strong>Solution:</strong> Ajoute des projets via l\'interface admin.</p>' .
                        '</div>',
                        Response::HTTP_OK
                    );
                }
                
                return $this->render('project/index.html.twig', [
                    'projects' => $projects,
                ]);
                
            } catch (\Exception $e) {
                if ($attempt < $maxRetries) {
                    // Attendre avant retry
                    sleep($retryDelay);
                    continue;
                }
                
                // Dernier essai echoue
                return new Response(
                    '<div style="font-family: Poppins; padding: 40px;">' .
                    '<h1 style="color: #f4a261;">MIDDO Platform - Mode Debug</h1>' .
                    '<p><strong>Erreur detectee:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>' .
                    '<p><strong>Tentatives:</strong> ' . $maxRetries . '</p>' .
                    '<p><strong>Solution:</strong> DB en spindown. Page se rechargera automatiquement dans 10 secondes.</p>' .
                    '<script>setTimeout(() => location.reload(), 10000);</script>' .
                    '</div>',
                    Response::HTTP_SERVICE_UNAVAILABLE
                );
            }
        }
        
        return new Response('Erreur inattendue', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}