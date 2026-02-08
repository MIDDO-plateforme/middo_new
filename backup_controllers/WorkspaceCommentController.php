<?php

namespace App\Controller;

use App\Entity\Workspace;
use App\Entity\WorkspaceComment;
use App\Entity\WorkspaceActivity;
use App\Form\WorkspaceCommentType;
use App\Repository\WorkspaceCommentRepository;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/workspace/{workspaceId}/comment')]
#[IsGranted('ROLE_USER')]
class WorkspaceCommentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorkspaceCommentRepository $commentRepository,
        private WorkspaceRepository $workspaceRepository
    ) {}

    /**
     * Liste tous les commentaires d'une entité
     */
    #[Route('/{entityType}/{entityId}', name: 'app_workspace_comment_list', methods: ['GET'])]
    public function list(int $workspaceId, string $entityType, int $entityId): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            throw $this->createNotFoundException('Workspace non trouvé');
        }

        // Valide le type d'entité
        if (!in_array($entityType, ['document', 'project', 'task'])) {
            throw $this->createNotFoundException('Type d\'entité invalide');
        }

        $comments = $this->commentRepository->findBy(
            [
                'workspace' => $workspace,
                'entityType' => $entityType,
                'entityId' => $entityId,
                'deletedAt' => null
            ],
            ['createdAt' => 'ASC']
        );

        return $this->render('workspace/comment/list.html.twig', [
            'workspace' => $workspace,
            'comments' => $comments,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ]);
    }

    /**
     * Crée un nouveau commentaire (API JSON)
     */
    #[Route('/create', name: 'app_workspace_comment_create', methods: ['POST'])]
    public function create(int $workspaceId, Request $request): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            return $this->json(['error' => 'Workspace not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        $entityType = $data['entity_type'] ?? null;
        $entityId = $data['entity_id'] ?? null;
        $content = $data['content'] ?? null;

        // Validation
        if (!$entityType || !$entityId || !$content) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($entityType, ['document', 'project', 'task'])) {
            return $this->json(['error' => 'Invalid entity type'], Response::HTTP_BAD_REQUEST);
        }

        // Crée le commentaire
        $comment = new WorkspaceComment();
        $comment->setWorkspace($workspace);
        $comment->setAuthor($this->getUser());
        $comment->setEntityType($entityType);
        $comment->setEntityId($entityId);
        $comment->setContent($content);
        $comment->setAiAnalyzed(false);

        $this->entityManager->persist($comment);
        
        // Log de l'activité
        $this->logActivity($workspace, 'comment_created', $comment);
        
        $this->entityManager->flush();

        // Analyse IA du sentiment (asynchrone simulé)
        $this->analyzeCommentSentiment($comment);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'comment' => [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'author' => $comment->getAuthor()->getEmail(),
                'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'ai_metadata' => $comment->getAiMetadata()
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Modifie un commentaire existant
     */
    #[Route('/{id}/edit', name: 'app_workspace_comment_edit', methods: ['GET', 'POST'])]
    public function edit(int $workspaceId, Request $request, WorkspaceComment $comment): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if ($comment->getWorkspace() !== $workspace) {
            throw $this->createNotFoundException('Commentaire non trouvé dans ce workspace');
        }

        // Seul l'auteur peut modifier son commentaire
        if ($comment->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres commentaires.');
            return $this->redirectToRoute('app_workspace_comment_list', [
                'workspaceId' => $workspaceId,
                'entityType' => $comment->getEntityType(),
                'entityId' => $comment->getEntityId()
            ]);
        }

        $oldContent = $comment->getContent();

        $form = $this->createForm(WorkspaceCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Réanalyse IA si le contenu a changé
            if ($oldContent !== $comment->getContent()) {
                $this->analyzeCommentSentiment($comment);
                
                $this->logActivity($workspace, 'comment_updated', $comment, [
                    'old_content' => substr($oldContent, 0, 100),
                    'new_content' => substr($comment->getContent(), 0, 100)
                ]);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Commentaire modifié avec succès !');
            return $this->redirectToRoute('app_workspace_comment_list', [
                'workspaceId' => $workspaceId,
                'entityType' => $comment->getEntityType(),
                'entityId' => $comment->getEntityId()
            ]);
        }

        return $this->render('workspace/comment/edit.html.twig', [
            'workspace' => $workspace,
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    /**
     * Modifie un commentaire (API JSON)
     */
    #[Route('/{id}/update', name: 'app_workspace_comment_update', methods: ['PUT'])]
    public function update(int $workspaceId, Request $request, WorkspaceComment $comment): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if ($comment->getWorkspace() !== $workspace) {
            return $this->json(['error' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }

        if ($comment->getAuthor() !== $this->getUser()) {
            return $this->json(['error' => 'Permission denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $newContent = $data['content'] ?? null;

        if (!$newContent) {
            return $this->json(['error' => 'Content is required'], Response::HTTP_BAD_REQUEST);
        }

        $oldContent = $comment->getContent();
        $comment->setContent($newContent);

        // Réanalyse IA
        $this->analyzeCommentSentiment($comment);

        $this->logActivity($workspace, 'comment_updated', $comment, [
            'old_content' => substr($oldContent, 0, 100),
            'new_content' => substr($newContent, 0, 100)
        ]);

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'comment' => [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'updated_at' => $comment->getUpdatedAt()->format('Y-m-d H:i:s'),
                'ai_metadata' => $comment->getAiMetadata()
            ]
        ]);
    }

    /**
     * Supprime un commentaire (soft delete)
     */
    #[Route('/{id}/delete', name: 'app_workspace_comment_delete', methods: ['POST'])]
    public function delete(int $workspaceId, Request $request, WorkspaceComment $comment): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if ($comment->getWorkspace() !== $workspace) {
            throw $this->createNotFoundException('Commentaire non trouvé dans ce workspace');
        }

        // Seul l'auteur peut supprimer son commentaire
        if ($comment->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez supprimer que vos propres commentaires.');
            return $this->redirectToRoute('app_workspace_comment_list', [
                'workspaceId' => $workspaceId,
                'entityType' => $comment->getEntityType(),
                'entityId' => $comment->getEntityId()
            ]);
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            // Soft delete
            $comment->setDeletedAt(new \DateTimeImmutable());
            
            $this->logActivity($workspace, 'comment_deleted', $comment);
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        }

        return $this->redirectToRoute('app_workspace_comment_list', [
            'workspaceId' => $workspaceId,
            'entityType' => $comment->getEntityType(),
            'entityId' => $comment->getEntityId()
        ]);
    }

    /**
     * Supprime un commentaire (API JSON)
     */
    #[Route('/{id}/remove', name: 'app_workspace_comment_remove', methods: ['DELETE'])]
    public function remove(int $workspaceId, WorkspaceComment $comment): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if ($comment->getWorkspace() !== $workspace) {
            return $this->json(['error' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }

        if ($comment->getAuthor() !== $this->getUser()) {
            return $this->json(['error' => 'Permission denied'], Response::HTTP_FORBIDDEN);
        }

        // Soft delete
        $comment->setDeletedAt(new \DateTimeImmutable());
        
        $this->logActivity($workspace, 'comment_deleted', $comment);
        
        $this->entityManager->flush();

        return $this->json(['success' => true]);
    }

    /**
     * Récupère les commentaires d'une entité (API JSON)
     */
    #[Route('/api/{entityType}/{entityId}', name: 'app_workspace_comment_api_list', methods: ['GET'])]
    public function apiList(int $workspaceId, string $entityType, int $entityId): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            return $this->json(['error' => 'Workspace not found'], Response::HTTP_NOT_FOUND);
        }

        if (!in_array($entityType, ['document', 'project', 'task'])) {
            return $this->json(['error' => 'Invalid entity type'], Response::HTTP_BAD_REQUEST);
        }

        $comments = $this->commentRepository->findBy(
            [
                'workspace' => $workspace,
                'entityType' => $entityType,
                'entityId' => $entityId,
                'deletedAt' => null
            ],
            ['createdAt' => 'ASC']
        );

        $commentsData = array_map(function(WorkspaceComment $comment) {
            return [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'author' => [
                    'id' => $comment->getAuthor()->getId(),
                    'email' => $comment->getAuthor()->getEmail(),
                    'username' => $comment->getAuthor()->getUsername()
                ],
                'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $comment->getUpdatedAt()->format('Y-m-d H:i:s'),
                'ai_metadata' => $comment->getAiMetadata()
            ];
        }, $comments);

        return $this->json([
            'success' => true,
            'comments' => $commentsData,
            'total' => count($commentsData)
        ]);
    }

    /**
     * Analyse le sentiment d'un commentaire avec IA (simulation)
     */
    private function analyzeCommentSentiment(WorkspaceComment $comment): void
    {
        $content = strtolower($comment->getContent());

        // Mots-clés positifs/négatifs (simulation simple)
        $positiveWords = ['bien', 'super', 'excellent', 'parfait', 'génial', 'bravo', 'merci', 'top'];
        $negativeWords = ['problème', 'erreur', 'bug', 'mauvais', 'nul', 'échec', 'impossible', 'urgent'];

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($content, $word);
        }

        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($content, $word);
        }

        // Calcul du score de sentiment (-1 à 1)
        $totalWords = $positiveCount + $negativeCount;
        $sentimentScore = $totalWords > 0 
            ? ($positiveCount - $negativeCount) / $totalWords 
            : 0;

        // Détermination du sentiment global
        $sentiment = 'neutral';
        if ($sentimentScore > 0.3) {
            $sentiment = 'positive';
        } elseif ($sentimentScore < -0.3) {
            $sentiment = 'negative';
        }

        // Extraction de mentions (@username)
        preg_match_all('/@(\w+)/', $comment->getContent(), $mentions);

        $aiMetadata = [
            'sentiment' => $sentiment,
            'sentiment_score' => round($sentimentScore, 2),
            'positive_keywords' => $positiveCount,
            'negative_keywords' => $negativeCount,
            'mentions' => $mentions[1] ?? [],
            'analyzed_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'word_count' => str_word_count($comment->getContent())
        ];

        $comment->setAiMetadata($aiMetadata);
        $comment->setAiAnalyzed(true);
    }

    /**
     * Log une activité dans le workspace
     */
    private function logActivity(Workspace $workspace, string $actionType, WorkspaceComment $comment, array $metadata = []): void
    {
        $activity = new WorkspaceActivity();
        $activity->setWorkspace($workspace);
        $activity->setUser($this->getUser());
        $activity->setActionType($actionType);
        $activity->setEntityType('comment');
        $activity->setEntityId($comment->getId());
        $activity->setMetadata(array_merge([
            'entity_type' => $comment->getEntityType(),
            'entity_id' => $comment->getEntityId(),
            'content_preview' => substr($comment->getContent(), 0, 100)
        ], $metadata));

        $this->entityManager->persist($activity);
    }
}
