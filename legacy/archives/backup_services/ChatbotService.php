<?php

namespace App\Service;

class ChatbotService
{
    private $geminiService;
    private $conversationHistory = [];

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function sendMessage(string $message, int $userId): array
    {
        // Ajouter au contexte
        $context = [
            'userId' => $userId,
            'history' => array_slice($this->conversationHistory, -5), // 5 derniers messages
        ];

        // Appeler Gemini
        $geminiResponse = $this->geminiService->generateResponse($message, $context);

        $response = $geminiResponse['response'];
        $mode = $geminiResponse['mode'];

        // Sauvegarder dans l'historique
        $this->conversationHistory[] = [
            'role' => 'user',
            'content' => $message,
            'timestamp' => time(),
        ];

        $this->conversationHistory[] = [
            'role' => 'assistant',
            'content' => $response,
            'timestamp' => time(),
            'mode' => $mode,
        ];

        return [
            'success' => true,
            'message' => $response,
            'timestamp' => time(),
            'conversationId' => $userId,
            'mode' => $mode,
        ];
    }

    public function getConversationHistory(int $userId): array
    {
        return $this->conversationHistory;
    }

    public function clearHistory(int $userId): bool
    {
        $this->conversationHistory = [];
        return true;
    }
}