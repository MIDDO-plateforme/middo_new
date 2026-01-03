<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'api_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'API MIDDO fonctionne !',
            'session_22' => 'Notifications OK',
            'timestamp' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }
}
