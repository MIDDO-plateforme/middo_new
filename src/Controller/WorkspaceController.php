<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Workspace;
use App\Entity\User;
use App\Form\WorkspaceType;
use App\Repository\WorkspaceRepository;
use App\Service\AI\SmartSuggestionsService;
use App\Service\AI\PredictiveMetricsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/workspace')]
#[IsGranted('ROLE_USER')]
class WorkspaceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly SmartSuggestionsService $suggestionsService,
        private readonly PredictiveMetricsService $metricsService
    ) {}

    #[Route('', name: 'workspace_index', methods: ['GET'])]
    public function index(WorkspaceRepository $repository): Response
    {
        $workspaces = $repository->findByUser($this->getUser());

        return $this->render('workspace/index.html.twig', [
            'workspaces' => $workspaces
        ]);
    }

    #[Route('/new', name: 'workspace_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $workspace = new Workspace();
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workspace->setOwner($this->getUser());
            
            $this->entityManager->persist($workspace);
            $this->entityManager->flush();

            $this->addFlash('success', 'Workspace créé avec succès.');
            
            return $this->redirectToRoute('workspace_show', ['id' => $workspace->getId()]);
        }

        return $this->render('workspace/new.html.twig', [
            'workspace' => $workspace,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'workspace_show', methods: ['GET'])]
    public function show(Workspace $workspace): Response
    {
        $this->denyAccessUnlessGranted('view', $workspace);

        return $this->render('workspace/show.html.twig', [
            'workspace' => $workspace
        ]);
    }

    #[Route('/{id}/edit', name: 'workspace_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Workspace $workspace): Response
    {
        $this->denyAccessUnlessGranted('edit', $workspace);

        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Workspace modifié avec succès.');
            
            return $this->redirectToRoute('workspace_show', ['id' => $workspace->getId()]);
        }

        return $this->render('workspace/edit.html.twig', [
            'workspace' => $workspace,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'workspace_delete', methods: ['POST'])]
    public function delete(Request $request, Workspace $workspace): Response
    {
        $this->denyAccessUnlessGranted('delete', $workspace);

        if ($this->isCsrfTokenValid('delete'.$workspace->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($workspace);
            $this->entityManager->flush();

            $this->addFlash('success', 'Workspace supprimé avec succès.');
        }

        return $this->redirectToRoute('workspace_index');
    }
}
