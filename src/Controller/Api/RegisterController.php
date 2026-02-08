<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        // Vérifier si l'utilisateur existe déjà
        if ($em->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already used'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'status' => 'user_created',
            'email' => $user->getEmail()
        ], 201);
    }
}
