<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectAnalysisController extends AbstractController
{
    #[Route('/project/{id}/analyze', name: 'app_project_analyze')]
    public function analyze(int $id, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);
        
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }
        
        return $this->render('project/analyze.html.twig', [
            'project' => $project,
        ]);
    }
}