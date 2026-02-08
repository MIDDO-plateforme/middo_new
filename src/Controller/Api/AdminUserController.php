<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    #[Route('/api/admin/users', name: 'api_admin_users', methods: ['GET'])]
    public function listUsers(EntityManagerInterface $em): JsonResponse
    {
        // Vérifier que l'utilisateur a ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $em->getRepository(User::class)->findAll();

        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/admin/users/{id}', name: 'api_admin_user_show', methods: ['GET'])]
    public function showUser(int $id, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier que l'utilisateur a ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ]);
    }

    #[Route('/api/admin/users/{id}', name: 'api_admin_user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier que l'utilisateur a ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(['status' => 'user_deleted'], 200);
    }

    #[Route('/api/admin/users/{id}', name: 'api_admin_user_update', methods: ['PATCH'])]
    public function updateUser(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier que l'utilisateur a ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Mise à jour email
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        // Mise à jour prénom
        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        // Mise à jour nom
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        // Mise à jour des rôles (optionnel)
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        $em->flush();

        return new JsonResponse([
            'status' => 'user_updated',
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ]);
    }

    #[Route('/api/admin/users', name: 'api_admin_user_create', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // Vérifier que l'utilisateur a ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        // Vérifier si l'email existe déjà
        if ($em->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already used'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        // Champs optionnels
        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        // Rôles (par défaut ROLE_USER)
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->setRoles($data['roles']);
        } else {
            $user->setRoles(['ROLE_USER']);
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'status' => 'user_created',
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ], 201);
    }

}
