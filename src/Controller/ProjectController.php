<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/projects', name: 'api_projects_')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $projects = $this->projects->findAll();
        $data = array_map(fn(Project $p) => $this->serializeProject($p), $projects);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        $project = new Project();
        $project->setName($payload['name'] ?? '');
        $project->setDescription($payload['description'] ?? null);
        $project->setStatus($payload['status'] ?? 'draft');

        /** @var User|null $user */
        $user = $this->getUser();
        if ($user) {
            $project->setOwner($user);
        }

        if (!empty($payload['members']) && is_array($payload['members'])) {
            foreach ($payload['members'] as $memberId) {
                $member = $this->em->getRepository(User::class)->find($memberId);
                if ($member) {
                    $project->addMember($member);
                }
            }
        }

        $errors = $this->validator->validate($project);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->projects->save($project, true);

        return $this->json($this->serializeProject($project), 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $project = $this->projects->find($id);

        if (!$project) {
            return $this->json(['error' => 'Projet introuvable'], 404);
        }

        return $this->json($this->serializeProject($project));
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $project = $this->projects->find($id);

        if (!$project) {
            return $this->json(['error' => 'Projet introuvable'], 404);
        }

        $payload = json_decode($request->getContent(), true);

        if (isset($payload['name'])) {
            $project->setName($payload['name']);
        }

        if (array_key_exists('description', $payload)) {
            $project->setDescription($payload['description']);
        }

        if (isset($payload['status'])) {
            $project->setStatus($payload['status']);
        }

        if (isset($payload['members']) && is_array($payload['members'])) {
            foreach ($project->getMembers() as $existing) {
                $project->removeMember($existing);
            }

            foreach ($payload['members'] as $memberId) {
                $member = $this->em->getRepository(User::class)->find($memberId);
                if ($member) {
                    $project->addMember($member);
                }
            }
        }

        $project->setUpdatedAt();

        $errors = $this->validator->validate($project);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->projects->save($project, true);

        return $this->json($this->serializeProject($project));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $project = $this->projects->find($id);

        if (!$project) {
            return $this->json(['error' => 'Projet introuvable'], 404);
        }

        $this->projects->remove($project, true);

        return $this->json(['message' => 'Projet supprimé']);
    }

    private function serializeProject(Project $p): array
    {
        return [
            'id' => $p->getId(),
            'name' => $p->getName(),
            'description' => $p->getDescription(),
            'status' => $p->getStatus(),
            'owner' => $p->getOwner()?->getId(),
            'members' => array_map(fn(User $u) => $u->getId(), $p->getMembers()->toArray()),
            'createdAt' => $p->getCreatedAt()->format(\DATE_ATOM),
            'updatedAt' => $p->getUpdatedAt()->format(\DATE_ATOM),
        ];
    }
}
