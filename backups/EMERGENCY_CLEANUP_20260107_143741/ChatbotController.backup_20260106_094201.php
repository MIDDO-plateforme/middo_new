<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    #[Route('/api/chatbot', name: 'api_chatbot', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $message = $data['message'] ?? '';

            if (empty(trim($message))) {
                return $this->json(['error' => 'Message vide'], 400);
            }

            $response = $this->generateSimpleResponse($message);

            return $this->json([
                'response' => $response,
                'timestamp' => time(),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur serveur: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateSimpleResponse(string $message): string
    {
        $message = strtolower(trim($message));

        $responses = [
            'bonjour' => "Bonjour ! Bienvenue sur MIDDO ! Comment puis-je t'aider ?",
            'salut' => "Salut ! Je suis la pour t'accompagner sur MIDDO !",
            'hello' => "Hello! How can I help you with MIDDO?",
            'aide' => "Je peux t'aider avec : Trouver des collaborateurs, Creer un projet, Naviguer dans MIDDO",
            'projet' => "Pour creer un projet, clique sur Explorer puis Nouveau Projet !",
            'merci' => "Avec plaisir ! N'hesite pas si tu as d'autres questions !",
        ];

        foreach ($responses as $keyword => $response) {
            if (str_contains($message, $keyword)) {
                return $response;
            }
        }

        return "J'ai bien recu ton message. Je suis en phase d'apprentissage !";
    }
}
