<?php

namespace App\Bundle\MIDDOGeniusProBundle\Controller;

use App\Bundle\MIDDOGeniusProBundle\Service\GeniusProApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeniusProController extends AbstractController
{
    private GeniusProApiService $geniusProService;

    public function __construct(GeniusProApiService $geniusProService)
    {
        $this->geniusProService = $geniusProService;
    }

    /**
     * Page principale Genius Pro
     */
    #[Route('/', name: 'genius_pro_index', methods: ['GET'])]
    public function index(): Response
    {
        $expertises = $this->geniusProService->getExpertises();
        $isHealthy = $this->geniusProService->checkHealth();

        return $this->render('genius_pro/index.html.twig', [
            'expertises' => $expertises,
            'server_status' => $isHealthy ? 'online' : 'offline'
        ]);
    }

    /**
     * API: Envoyer un message
     */
    #[Route('/api/chat', name: 'genius_pro_api_chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['message']) || empty(trim($data['message']))) {
            return $this->json([
                'error' => 'Message requis'
            ], 400);
        }

        $message = trim($data['message']);
        $expertise = $data['expertise'] ?? 'fullstack';
        $context = $data['context'] ?? null;

        // LOG 1: Requête reçue
        error_log("=== GENIUS PRO CHAT REQUEST ===");
        error_log("Message: " . $message);
        error_log("Expertise: " . $expertise);
        error_log("Context: " . ($context ?? 'null'));

        try {
            $response = $this->geniusProService->sendMessage($message, $expertise, $context);

            // LOG 2: Réponse du service
            error_log("Response from service: " . json_encode($response));

            if ($response === null) {
                error_log("ERROR: Service returned NULL");
                return $this->json([
                    'error' => 'Serveur Genius Pro inaccessible'
                ], 503);
            }

            return $this->json($response);

        } catch (\Exception $e) {
            error_log("EXCEPTION: " . $e->getMessage());
            error_log("TRACE: " . $e->getTraceAsString());
            
            return $this->json([
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Health check
     */
    #[Route('/api/health', name: 'genius_pro_api_health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $isHealthy = $this->geniusProService->checkHealth();

        return $this->json([
            'status' => $isHealthy ? 'online' : 'offline',
            'timestamp' => time()
        ]);
    }

    /**
     * API: Liste des expertises
     */
    #[Route('/api/expertises', name: 'genius_pro_api_expertises', methods: ['GET'])]
    public function expertises(): JsonResponse
    {
        return $this->json($this->geniusProService->getExpertises());
    }
}
