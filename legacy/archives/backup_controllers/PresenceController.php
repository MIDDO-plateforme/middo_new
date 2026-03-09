<?php

namespace App\Controller;

use App\Service\WebSocketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/presence', name: 'api_presence_')]
class PresenceController extends AbstractController
{
    private WebSocketService $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        $this->webSocketService = $webSocketService;
    }

    #[Route('/online', name: 'online', methods: ['GET'])]
    public function getOnlineUsers(): JsonResponse
    {
        try {
            $onlineUsers = $this->webSocketService->getConnectedUsers();

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'online_users' => $onlineUsers,
                    'count' => count($onlineUsers),
                ],
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/status/{userId}', name: 'status', methods: ['GET'])]
    public function getUserStatus(int $userId): JsonResponse
    {
        try {
            $isOnline = $this->webSocketService->isUserConnected($userId);

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'user_id' => $userId,
                    'is_online' => $isOnline,
                    'status' => $isOnline ? 'online' : 'offline',
                ],
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/heartbeat', name: 'heartbeat', methods: ['POST'])]
    public function heartbeat(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userId = $data['user_id'] ?? null;

            if (!$userId) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'user_id required',
                ], 400);
            }

            $this->webSocketService->heartbeat($userId);

            return new JsonResponse([
                'success' => true,
                'message' => 'Heartbeat received',
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/connect', name: 'connect', methods: ['POST'])]
    public function connect(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userId = $data['user_id'] ?? null;
            $metadata = $data['metadata'] ?? [];

            if (!$userId) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'user_id required',
                ], 400);
            }

            $this->webSocketService->userConnected($userId, $metadata);

            return new JsonResponse([
                'success' => true,
                'message' => 'User connected',
                'user_id' => $userId,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/disconnect', name: 'disconnect', methods: ['POST'])]
    public function disconnect(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userId = $data['user_id'] ?? null;

            if (!$userId) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'user_id required',
                ], 400);
            }

            $this->webSocketService->userDisconnected($userId);

            return new JsonResponse([
                'success' => true,
                'message' => 'User disconnected',
                'user_id' => $userId,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}