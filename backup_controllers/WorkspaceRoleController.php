<?php

namespace App\Controller;

use App\Entity\Workspace;
use App\Entity\WorkspaceRole;
use App\Entity\WorkspaceActivity;
use App\Form\WorkspaceRoleType;
use App\Repository\WorkspaceRoleRepository;
use App\Repository\WorkspaceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/workspace/{workspaceId}/role')]
#[IsGranted('ROLE_USER')]
class WorkspaceRoleController extends AbstractController
{
    // Définition des permissions disponibles
    private const PERMISSIONS = [
        'workspace.edit' => 'Modifier le workspace',
        'workspace.delete' => 'Supprimer le workspace',
        'workspace.invite' => 'Inviter des collaborateurs',
        'workspace.manage_roles' => 'Gérer les rôles',
        'document.create' => 'Créer des documents',
        'document.edit' => 'Modifier des documents',
        'document.delete' => 'Supprimer des documents',
        'document.view' => 'Voir les documents',
        'project.create' => 'Créer des projets',
        'project.edit' => 'Modifier des projets',
        'project.delete' => 'Supprimer des projets',
        'project.view' => 'Voir les projets',
        'project.manage_team' => 'Gérer l\'équipe projet',
        'task.create' => 'Créer des tâches',
        'task.edit' => 'Modifier des tâches',
        'task.delete' => 'Supprimer des tâches',
        'task.view' => 'Voir les tâches',
        'task.assign' => 'Assigner des tâches',
        'comment.create' => 'Créer des commentaires',
        'comment.edit' => 'Modifier des commentaires',
        'comment.delete' => 'Supprimer des commentaires',
        'comment.view' => 'Voir les commentaires',
        'activity.view' => 'Voir l\'historique d\'activités',
        'analytics.view' => 'Voir les statistiques',
        'settings.manage' => 'Gérer les paramètres'
    ];

    // Rôles prédéfinis
    private const ROLE_PRESETS = [
        'OWNER' => [
            'workspace.edit', 'workspace.delete', 'workspace.invite', 'workspace.manage_roles',
            'document.create', 'document.edit', 'document.delete', 'document.view',
            'project.create', 'project.edit', 'project.delete', 'project.view', 'project.manage_team',
            'task.create', 'task.edit', 'task.delete', 'task.view', 'task.assign',
            'comment.create', 'comment.edit', 'comment.delete', 'comment.view',
            'activity.view', 'analytics.view', 'settings.manage'
        ],
        'ADMIN' => [
            'workspace.edit', 'workspace.invite',
            'document.create', 'document.edit', 'document.delete', 'document.view',
            'project.create', 'project.edit', 'project.delete', 'project.view', 'project.manage_team',
            'task.create', 'task.edit', 'task.delete', 'task.view', 'task.assign',
            'comment.create', 'comment.edit', 'comment.delete', 'comment.view',
            'activity.view', 'analytics.view'
        ],
        'MEMBER' => [
            'document.create', 'document.edit', 'document.view',
            'project.view', 'project.manage_team',
            'task.create', 'task.edit', 'task.view', 'task.assign',
            'comment.create', 'comment.edit', 'comment.view',
            'activity.view'
        ],
        'VIEWER' => [
            'document.view',
            'project.view',
            'task.view',
            'comment.view',
            'activity.view'
        ]
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorkspaceRoleRepository $roleRepository,
        private WorkspaceRepository $workspaceRepository,
        private UserRepository $userRepository
    ) {}

    /**
     * Liste tous les rôles/permissions d'un workspace
     */
    #[Route('/', name: 'app_workspace_role_index', methods: ['GET'])]
    public function index(int $workspaceId): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            throw $this->createNotFoundException('Workspace non trouvé');
        }

        // Vérifie la permission de gestion des rôles
        if (!$this->hasPermission($workspace, 'workspace.manage_roles')) {
            $this->addFlash('error', 'Vous n\'avez pas la permission de gérer les rôles.');
            return $this->redirectToRoute('app_workspace_show', ['id' => $workspaceId]);
        }

        $roles = $this->roleRepository->findBy(
            ['workspace' => $workspace],
            ['createdAt' => 'DESC']
        );

        return $this->render('workspace/role/index.html.twig', [
            'workspace' => $workspace,
            'roles' => $roles,
            'available_permissions' => self::PERMISSIONS,
        ]);
    }

    /**
     * Invite un nouveau collaborateur
     */
    #[Route('/invite', name: 'app_workspace_role_invite', methods: ['GET', 'POST'])]
    public function invite(int $workspaceId, Request $request): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);
        
        if (!$workspace) {
            throw $this->createNotFoundException('Workspace non trouvé');
        }

        if (!$this->hasPermission($workspace, 'workspace.invite')) {
            $this->addFlash('error', 'Vous n\'avez pas la permission d\'inviter des collaborateurs.');
            return $this->redirectToRoute('app_workspace_show', ['id' => $workspaceId]);
        }

        $role = new WorkspaceRole();
        $role->setWorkspace($workspace);
        $role->setRole('MEMBER'); // Rôle par défaut

        $form = $this->createForm(WorkspaceRoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie que l'utilisateur n'a pas déjà un rôle dans ce workspace
            $existingRole = $this->roleRepository->findOneBy([
                'workspace' => $workspace,
                'user' => $role->getUser()
            ]);

            if ($existingRole) {
                $this->addFlash('error', 'Cet utilisateur a déjà un rôle dans ce workspace.');
                return $this->redirectToRoute('app_workspace_role_invite', ['workspaceId' => $workspaceId]);
            }

            // Applique les permissions prédéfinies selon le rôle
            $roleType = $role->getRole();
            if (isset(self::ROLE_PRESETS[$roleType])) {
                $role->setPermissions(self::ROLE_PRESETS[$roleType]);
            }

            // Ajoute l'utilisateur aux collaborateurs du workspace
            $workspace->addCollaborator($role->getUser());

            $this->entityManager->persist($role);
            
            // Log de l'activité
            $this->logActivity($workspace, 'collaborator_invited', [
                'invited_user' => $role->getUser()->getEmail(),
                'role' => $roleType
            ]);
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Collaborateur invité avec succès !');
            return $this->redirectToRoute('app_workspace_role_index', ['workspaceId' => $workspaceId]);
        }

        return $this->render('workspace/role/invite.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
            'role_presets' => array_keys(self::ROLE_PRESETS),
        ]);
    }

    /**
     * Modifie les permissions d'un rôle
     */
    #[Route('/{id}/edit', name: 'app_workspace_role_edit', methods: ['GET', 'POST'])]
    public function edit(int $workspaceId, Request $request, WorkspaceRole $role): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if ($role->getWorkspace() !== $workspace) {
            throw $this->createNotFoundException('Rôle non trouvé dans ce workspace');
        }

        if (!$this->hasPermission($workspace, 'workspace.manage_roles')) {
            $this->addFlash('error', 'Vous n\'avez pas la permission de modifier les rôles.');
            return $this->redirectToRoute('app_workspace_show', ['id' => $workspaceId]);
        }

        // Empêche de modifier le rôle OWNER
        if ($role->getRole() === 'OWNER') {
            $this->addFlash('error', 'Le rôle OWNER ne peut pas être modifié.');
            return $this->redirectToRoute('app_workspace_role_index', ['workspaceId' => $workspaceId]);
        }

        $oldPermissions = $role->getPermissions();

        $form = $this->createForm(WorkspaceRoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Applique les permissions prédéfinies si le rôle change
            $roleType = $role->getRole();
            if (isset(self::ROLE_PRESETS[$roleType])) {
                $role->setPermissions(self::ROLE_PRESETS[$roleType]);
            }

            $this->logActivity($workspace, 'role_updated', [
                'user' => $role->getUser()->getEmail(),
                'old_role' => $oldPermissions,
                'new_role' => $role->getPermissions()
            ]);

            $this->entityManager->flush();

            $this->addFlash('success', 'Rôle modifié avec succès !');
            return $this->redirectToRoute('app_workspace_role_index', ['workspaceId' => $workspaceId]);
        }

        return $this->render('workspace/role/edit.html.twig', [
            'workspace' => $workspace,
            'role' => $role,
            'form' => $form,
            'available_permissions' => self::PERMISSIONS,
        ]);
    }

    /**
     * Révoque l'accès d'un collaborateur
     */
    #[Route('/{id}/revoke', name: 'app_workspace_role_revoke', methods: ['POST'])]
    public function revoke(int $workspaceId, Request $request, WorkspaceRole $role): Response
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if ($role->getWorkspace() !== $workspace) {
            throw $this->createNotFoundException('Rôle non trouvé dans ce workspace');
        }

        if (!$this->hasPermission($workspace, 'workspace.manage_roles')) {
            $this->addFlash('error', 'Vous n\'avez pas la permission de révoquer des accès.');
            return $this->redirectToRoute('app_workspace_show', ['id' => $workspaceId]);
        }

        // Empêche de révoquer le rôle OWNER
        if ($role->getRole() === 'OWNER') {
            $this->addFlash('error', 'Le rôle OWNER ne peut pas être révoqué.');
            return $this->redirectToRoute('app_workspace_role_index', ['workspaceId' => $workspaceId]);
        }

        if ($this->isCsrfTokenValid('revoke'.$role->getId(), $request->request->get('_token'))) {
            $user = $role->getUser();
            
            // Retire l'utilisateur des collaborateurs
            $workspace->removeCollaborator($user);

            $this->logActivity($workspace, 'collaborator_revoked', [
                'revoked_user' => $user->getEmail(),
                'role' => $role->getRole()
            ]);

            $this->entityManager->remove($role);
            $this->entityManager->flush();

            $this->addFlash('success', 'Accès révoqué avec succès.');
        }

        return $this->redirectToRoute('app_workspace_role_index', ['workspaceId' => $workspaceId]);
    }

    /**
     * Récupère les permissions d'un utilisateur (API JSON)
     */
    #[Route('/check/{userId}', name: 'app_workspace_role_check', methods: ['GET'])]
    public function checkPermissions(int $workspaceId, int $userId): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if (!$workspace) {
            return $this->json(['error' => 'Workspace not found'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->userRepository->find($userId);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $role = $this->roleRepository->findOneBy([
            'workspace' => $workspace,
            'user' => $user
        ]);

        if (!$role) {
            return $this->json([
                'has_access' => false,
                'permissions' => []
            ]);
        }

        return $this->json([
            'has_access' => true,
            'role' => $role->getRole(),
            'permissions' => $role->getPermissions()
        ]);
    }

    /**
     * Met à jour les permissions d'un rôle (API JSON)
     */
    #[Route('/{id}/permissions', name: 'app_workspace_role_update_permissions', methods: ['POST'])]
    public function updatePermissions(int $workspaceId, Request $request, WorkspaceRole $role): JsonResponse
    {
        $workspace = $this->workspaceRepository->find($workspaceId);

        if ($role->getWorkspace() !== $workspace) {
            return $this->json(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$this->hasPermission($workspace, 'workspace.manage_roles')) {
            return $this->json(['error' => 'Permission denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $permissions = $data['permissions'] ?? [];

        // Valide que les permissions existent
        $validPermissions = array_keys(self::PERMISSIONS);
        $permissions = array_intersect($permissions, $validPermissions);

        $role->setPermissions($permissions);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'permissions' => $role->getPermissions()
        ]);
    }

    /**
     * Vérifie si l'utilisateur actuel a une permission
     */
    private function hasPermission(Workspace $workspace, string $permission): bool
    {
        $user = $this->getUser();

        // Le propriétaire a toutes les permissions
        if ($workspace->getUser() === $user) {
            return true;
        }

        $role = $this->roleRepository->findOneBy([
            'workspace' => $workspace,
            'user' => $user
        ]);

        if (!$role) {
            return false;
        }

        return in_array($permission, $role->getPermissions(), true);
    }

    /**
     * Log une activité dans le workspace
     */
    private function logActivity(Workspace $workspace, string $actionType, array $metadata = []): void
    {
        $activity = new WorkspaceActivity();
        $activity->setWorkspace($workspace);
        $activity->setUser($this->getUser());
        $activity->setActionType($actionType);
        $activity->setEntityType('role');
        $activity->setEntityId(0); // Pas d'entité spécifique pour les rôles
        $activity->setMetadata($metadata);

        $this->entityManager->persist($activity);
    }
}
