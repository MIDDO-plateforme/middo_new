<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projets')]
#[IsGranted('ROLE_USER')]
class ProjectController extends AbstractController
{
    #[Route('', name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        $projects = $projectRepository->findByUser($user);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/nouveau', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $project->setCreator($this->getUser());

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet créé avec succès !');

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        $this->denyAccessUnlessGranted('view', $project);

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $project);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Projet modifié avec succès !');

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $project);

        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet supprimé avec succès !');
        }

        return $this->redirectToRoute('app_project_index');
    }

    #[Route('/recherche', name: 'app_project_search', methods: ['GET'])]
    public function search(Request $request, ProjectRepository $projectRepository): Response
    {
        $query = $request->query->get('q', '');
        $user = $this->getUser();

        $projects = $query 
            ? $projectRepository->searchByName($query, $user)
            : $projectRepository->findByUser($user);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'query' => $query,
        ]);
    }

    #[Route('/filtre/{status}', name: 'app_project_filter', methods: ['GET'])]
    public function filter(string $status, ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        $projects = $projectRepository->findByStatus($status, $user);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            'current_filter' => $status,
        ]);
    }
}