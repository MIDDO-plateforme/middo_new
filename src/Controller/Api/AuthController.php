<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    /**
     * POST /api/register
     * Créer un nouveau compte utilisateur
     */
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation simple
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse([
                'error' => 'Missing required fields: name, email, password'
            ], 400);
        }

        // MODE MOCK : Retourner des données fictives (pas de BDD)
        $mockUser = [
            'id' => rand(1000, 9999),
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'freelance',
            'createdAt' => (new \DateTime())->format('Y-m-d\TH:i:s\Z')
        ];

        // Token fictif (base64 encode)
        $token = base64_encode($mockUser['email'] . ':' . time());

        return new JsonResponse([
            'token' => $token,
            'user' => $mockUser
        ], 201);
    }

    /**
     * POST /api/login
     * Connexion utilisateur
     */
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse([
                'error' => 'Missing email or password'
            ], 400);
        }

        // MODE MOCK : Accepter n'importe quel email/password
        $mockUser = [
            'id' => 1,
            'name' => 'Baudouin MBANE LOKOTA',
            'email' => $data['email'],
            'role' => 'freelance',
            'createdAt' => '2026-01-01T10:00:00Z'
        ];

        // Token fictif
        $token = base64_encode($mockUser['email'] . ':' . time());

        return new JsonResponse([
            'token' => $token,
            'user' => $mockUser
        ], 200);
    }
}
