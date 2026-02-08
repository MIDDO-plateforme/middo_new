<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Workspace;
use App\Entity\WorkspaceDocument;
use App\Entity\WorkspaceActivity;
use App\Form\WorkspaceDocumentType;
use App\Repository\WorkspaceDocumentRepository;
use App\Service\AI\SentimentAnalysisService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/workspace/{workspaceId}/document')]
#[IsGranted('ROLE_USER')]
class WorkspaceDocumentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly SentimentAnalysisService $sentimentService
    ) {}

    #[Route('', name: 'app_workspace_document_index', methods: ['GET'])]
    public function index(int $workspaceId, WorkspaceDocumentRepository $repository): Response
    {
        $workspace = $this->entityManager->getRepository(Workspace::class)->find($workspaceId);
        
        if (!$workspace) {
            throw $this->createNotFoundException('Workspace not found');
        }

        $this->denyAccessUnlessGranted('view', $workspace);

        $documents = $repository->findBy(['workspace' => $workspace]);

        return $this->render('workspace/document/index.html.twig', [
            'workspace' => $workspace,
            'documents' => $documents
        ]);
    }

    #[Route('/new', name: 'app_workspace_document_new', methods: ['GET', 'POST'])]
    public function new(int $workspaceId, Request $request): Response
    {
        $workspace = $this->entityManager->getRepository(Workspace::class)->find($workspaceId);
        
        if (!$workspace) {
            throw $this->createNotFoundException('Workspace not found');
        }

        $this->denyAccessUnlessGranted('edit', $workspace);

        $document = new WorkspaceDocument();
        $document->setWorkspace($workspace);
        $document->setCreatedBy($this->getUser());

        $form = $this->createForm(WorkspaceDocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($document);
            $this->entityManager->flush();

            $this->logActivity($workspace, 'document_created', [
                'document_id' => $document->getId(),
                'document_title' => $document->getTitle()
            ]);

            $this->addFlash('success', 'Document créé avec succès.');

            return $this->redirectToRoute('app_workspace_document_show', [
                'workspaceId' => $workspaceId,
                'id' => $document->getId()
            ]);
        }

        return $this->render('workspace/document/new.html.twig', [
            'workspace' => $workspace,
            'document' => $document,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_workspace_document_show', methods: ['GET'])]
    public function show(int $workspaceId, WorkspaceDocument $document): Response
    {
        if ($document->getWorkspace()->getId() !== $workspaceId) {
            throw $this->createNotFoundException('Document not found in this workspace');
        }

        $this->denyAccessUnlessGranted('view', $document->getWorkspace());

        return $this->render('workspace/document/show.html.twig', [
            'workspace' => $document->getWorkspace(),
            'document' => $document
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workspace_document_edit', methods: ['GET', 'POST'])]
    public function edit(int $workspaceId, Request $request, WorkspaceDocument $document): Response
    {
        if ($document->getWorkspace()->getId() !== $workspaceId) {
            throw $this->createNotFoundException('Document not found in this workspace');
        }

        $this->denyAccessUnlessGranted('edit', $document->getWorkspace());

        $form = $this->createForm(WorkspaceDocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->logActivity($document->getWorkspace(), 'document_updated', [
                'document_id' => $document->getId(),
                'document_title' => $document->getTitle()
            ]);

            $this->addFlash('success', 'Document modifié avec succès.');

            return $this->redirectToRoute('app_workspace_document_show', [
                'workspaceId' => $workspaceId,
                'id' => $document->getId()
            ]);
        }

        return $this->render('workspace/document/edit.html.twig', [
            'workspace' => $document->getWorkspace(),
            'document' => $document,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_workspace_document_delete', methods: ['POST'])]
    public function delete(int $workspaceId, Request $request, WorkspaceDocument $document): Response
    {
        if ($document->getWorkspace()->getId() !== $workspaceId) {
            throw $this->createNotFoundException('Document not found in this workspace');
        }

        $this->denyAccessUnlessGranted('delete', $document->getWorkspace());

        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $this->logActivity($document->getWorkspace(), 'document_deleted', [
                'document_id' => $document->getId(),
                'document_title' => $document->getTitle()
            ]);

            $this->entityManager->remove($document);
            $this->entityManager->flush();

            $this->addFlash('success', 'Document supprimé avec succès.');
        }

        return $this->redirectToRoute('app_workspace_document_index', [
            'workspaceId' => $workspaceId
        ]);
    }

    #[Route('/{id}/analyze', name: 'app_workspace_document_analyze', methods: ['POST'])]
    public function analyze(int $workspaceId, WorkspaceDocument $document): JsonResponse
    {
        if ($document->getWorkspace()->getId() !== $workspaceId) {
            throw $this->createNotFoundException('Document not found in this workspace');
        }

        $this->denyAccessUnlessGranted('view', $document->getWorkspace());

        try {
            $analysis = $this->sentimentService->analyze($document->getContent() ?? '');

            $this->logActivity($document->getWorkspace(), 'document_analyzed', [
                'document_id' => $document->getId(),
                'sentiment' => $analysis['sentiment'] ?? 'unknown'
            ]);

            return $this->json([
                'success' => true,
                'analysis' => $analysis
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Document analysis failed', [
                'document_id' => $document->getId(),
                'error' => $e->getMessage()
            ]);

            return $this->json([
                'success' => false,
                'error' => 'Analyse impossible'
            ], 500);
        }
    }

    private function logActivity(Workspace $workspace, string $type, array $metadata = []): void
    {
        $activity = new WorkspaceActivity();
        $activity->setWorkspace($workspace);
        $activity->setUser($this->getUser());
        $activity->setType($type);
        $activity->setMetadata(array_merge([
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ], $metadata));

        $this->entityManager->persist($activity);
    }
}
