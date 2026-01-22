<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    /**
     * POST /api/register
     * Créer un nouveau compte utilisateur
     */
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation simple
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse([
                'error' => 'Missing required fields: name, email, password'
            ], 400);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse([
                'error' => 'User with this email already exists'
            ], 409);
        }

        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNom($data['name']);
        $user->setRoles(['ROLE_USER']);

        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Persister dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'User created successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getNom()
            ]
        ], 201);
    }

    /**
     * POST /api/login
     * Connexion utilisateur
     * Note: Ce endpoint est géré par lexik/jwt-authentication-bundle via /api/login_check
     */
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Use /api/login_check for authentication'
        ], 200);
    }
}