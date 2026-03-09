<?php

namespace App\Controller\Auth;

use App\Application\User\LoginUserCommand;
use App\Application\User\LoginUserHandler;
use App\Infrastructure\Security\JWTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        LoginUserHandler $handler,
        JWTService $jwtService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        try {
            $command = new LoginUserCommand(
                email: $data['email'],
                password: $data['password']
            );

            $user = $handler->handle($command);

            $token = $jwtService->generateToken($user);

            return new JsonResponse([
                'token' => $token,
                'user' => [
                    'id' => $user->id(),
                    'email' => $user->email()->value(),
                ]
            ]);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }
    }
}
