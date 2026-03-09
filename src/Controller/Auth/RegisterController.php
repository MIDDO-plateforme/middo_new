<?php

namespace App\Controller\Auth;

use App\Application\User\RegisterUserCommand;
use App\Application\User\RegisterUserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, RegisterUserHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        $command = new RegisterUserCommand(
            email: $data['email'],
            password: $data['password'],
            iaSettings: $data['ia_settings'] ?? []
        );

        $user = $handler->handle($command);

        return new JsonResponse([
            'id' => $user->id(),
            'email' => $user->email()->value(),
        ], 201);
    }
}
