<?php

namespace App\Controller;

use App\Service\ChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    private $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    #[Route('/api/chatbot/message', name: 'api_chatbot_message', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $message = $data['message'] ?? '';
        $userId = $data['userId'] ?? 1;

        if (empty($message)) {
            return $this->json([
                'success' => false,
                'error' => 'Message vide',
            ], 400);
        }

        $response = $this->chatbotService->sendMessage($message, $userId);

        return $this->json($response);
    }

    #[Route('/api/chatbot/history', name: 'api_chatbot_history', methods: ['GET'])]
    public function getHistory(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId', 1);
        $history = $this->chatbotService->getConversationHistory($userId);

        return $this->json([
            'success' => true,
            'history' => $history,
            'count' => count($history),
        ]);
    }

    #[Route('/api/chatbot/clear', name: 'api_chatbot_clear', methods: ['POST'])]
    public function clearHistory(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['userId'] ?? 1;

        $success = $this->chatbotService->clearHistory($userId);

        return $this->json([
            'success' => $success,
            'message' => 'Historique effacé',
        ]);
    }
}