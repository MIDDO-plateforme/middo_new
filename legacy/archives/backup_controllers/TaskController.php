<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tasks', name: 'api_tasks_')]
class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/test', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'API Tasks MIDDO operationnelle',
            'session' => 'SESSION 40',
            'version' => '1.0.0',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->query->get('status'),
                'priority' => $request->query->get('priority'),
                'assignedTo' => $request->query->get('assignedTo'),
                'orderBy' => $request->query->get('orderBy', 'createdAt'),
                'order' => $request->query->get('order', 'DESC')
            ];

            $filters = array_filter($filters, fn($v) => $v !== null);

            $tasks = $this->taskRepository->findWithFilters($filters);

            return $this->json([
                'success' => true,
                'tasks' => array_map(fn($task) => $task->toArray(), $tasks),
                'count' => count($tasks),
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return $this->json(['success' => false, 'error' => 'Tache non trouvee'], 404);
        }

        return $this->json([
            'success' => true,
            'task' => $task->toArray(),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['title'])) {
                return $this->json(['success' => false, 'error' => 'Titre requis'], 400);
            }

            $task = new Task();
            $task->setTitle($data['title']);
            $task->setDescription($data['description'] ?? null);
            $task->setStatus($data['status'] ?? 'pending');
            $task->setPriority($data['priority'] ?? 'medium');

            if (isset($data['assignedTo'])) {
                $user = $this->userRepository->find($data['assignedTo']);
                if ($user) {
                    $task->setAssignedTo($user);
                }
            }

            if (isset($data['dueDate'])) {
                $task->setDueDate(new \DateTime($data['dueDate']));
            }

            if (isset($data['tags'])) {
                $task->setTags($data['tags']);
            }

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Tache creee avec succes',
                'task' => $task->toArray()
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $task = $this->taskRepository->find($id);

            if (!$task) {
                return $this->json(['success' => false, 'error' => 'Tache non trouvee'], 404);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['title'])) $task->setTitle($data['title']);
            if (isset($data['description'])) $task->setDescription($data['description']);
            if (isset($data['status'])) $task->setStatus($data['status']);
            if (isset($data['priority'])) $task->setPriority($data['priority']);

            if (isset($data['assignedTo'])) {
                $user = $this->userRepository->find($data['assignedTo']);
                $task->setAssignedTo($user);
            }

            if (isset($data['dueDate'])) {
                $task->setDueDate(new \DateTime($data['dueDate']));
            }

            if (isset($data['tags'])) {
                $task->setTags($data['tags']);
            }

            $task->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Tache mise a jour',
                'task' => $task->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $task = $this->taskRepository->find($id);

            if (!$task) {
                return $this->json(['success' => false, 'error' => 'Tache non trouvee'], 404);
            }

            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Tache supprimee avec succes'
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/status', name: 'update_status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        try {
            $task = $this->taskRepository->find($id);

            if (!$task) {
                return $this->json(['success' => false, 'error' => 'Tache non trouvee'], 404);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['status'])) {
                return $this->json(['success' => false, 'error' => 'Statut requis'], 400);
            }

            $task->setStatus($data['status']);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Statut mis a jour',
                'task' => $task->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/stats/summary', name: 'stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->taskRepository->getStatistics();
            $byStatus = $this->taskRepository->countByStatus();
            $byPriority = $this->taskRepository->countByPriority();

            return $this->json([
                'success' => true,
                'data' => [
                    'summary' => $stats,
                    'by_status' => $byStatus,
                    'by_priority' => $byPriority
                ],
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/user/{userId}', name: 'by_user', methods: ['GET'])]
    public function byUser(int $userId, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->find($userId);

            if (!$user) {
                return $this->json(['success' => false, 'error' => 'Utilisateur non trouve'], 404);
            }

            $status = $request->query->get('status');
            $tasks = $this->taskRepository->findByUser($user, $status);

            return $this->json([
                'success' => true,
                'tasks' => array_map(fn($task) => $task->toArray(), $tasks),
                'count' => count($tasks),
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
