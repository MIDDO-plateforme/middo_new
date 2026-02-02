<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectUiController extends AbstractController
{
    /**
     * Liste des projets (UI)
     */
    #[Route('/projects', name: 'app_project_list', methods: ['GET'])]
    public function list(ProjectRepository $projectRepository): Response
    {
        // TODO: Filtrer par utilisateur connecté
        $projects = $projectRepository->findAll();
        
        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * Afficher un projet (UI)
     */
    #[Route('/projects/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * Nouveau projet (UI - placeholder)
     */
    #[Route('/projects/new', name: 'app_project_new', methods: ['GET', 'POST'], priority: 1)]
    public function new(): Response
    {
        // TODO: Implémenter le formulaire de création
        $this->addFlash('info', 'Formulaire de création à implémenter.');
        return $this->redirectToRoute('app_project_list');
    }

    /**
     * Éditer un projet (UI - placeholder)
     */
    #[Route('/projects/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Project $project): Response
    {
        // TODO: Implémenter le formulaire d'édition
        $this->addFlash('info', 'Formulaire d\'édition à implémenter.');
        return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
    }
}