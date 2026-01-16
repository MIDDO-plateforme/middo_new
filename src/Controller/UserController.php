<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function list(Request $request, UserRepository $userRepository): Response
    {
        $typeFilter = $request->query->get('type');
        
        if ($typeFilter) {
            $users = $userRepository->findBy(['userType' => $typeFilter]);
        } else {
            $users = $userRepository->findAll();
        }
        
        $userCounts = [
            'entrepreneur' => $userRepository->count(['userType' => 'entrepreneur']),
            'investisseur' => $userRepository->count(['userType' => 'investisseur']),
            'entreprise' => $userRepository->count(['userType' => 'entreprise']),
            'particulier' => $userRepository->count(['userType' => 'particulier']),
            'association' => $userRepository->count(['userType' => 'association']),
            'institution' => $userRepository->count(['userType' => 'institution']),
            'inspecteur' => $userRepository->count(['userType' => 'inspecteur']),
        ];
        
        return $this->render('user/list.html.twig', [
            'users' => $users,
            'currentFilter' => $typeFilter,
            'userCounts' => $userCounts,
        ]);
    }
    
    #[Route('/user/{id}', name: 'app_user_show', requirements: ['id' => '\d+'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * GET /api/me
     * Récupérer les informations de l'utilisateur connecté
     */
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        // Récupérer le token depuis les headers
        $authHeader = $request->headers->get('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse([
                'error' => 'Missing or invalid token'
            ], 401);
        }

        // Extraire le token
        $token = substr($authHeader, 7);
        
        // Décoder le token (MODE MOCK)
        $decoded = base64_decode($token);
        $email = explode(':', $decoded)[0] ?? null;

        if (!$email) {
            return new JsonResponse([
                'error' => 'Invalid token'
            ], 401);
        }

        // Retourner un utilisateur fictif
        $mockUser = [
            'id' => 1,
            'name' => 'Baudouin MBANE LOKOTA',
            'email' => $email,
            'role' => 'freelance',
            'createdAt' => '2026-01-01T10:00:00Z'
        ];

        return new JsonResponse($mockUser, 200);
    }
}
