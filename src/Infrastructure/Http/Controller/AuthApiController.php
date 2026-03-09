<?php

namespace App\Infrastructure\Http\Controller;

use App\Application\Auth\Command\RegisterUserCommand;
use App\Application\Auth\Command\LoginUserCommand;
use App\Application\Auth\Command\RefreshTokenCommand;
use App\Application\Auth\Handler\RegisterUserHandler;
use App\Application\Auth\Handler\LoginUserHandler;
use App\Application\Auth\Handler\RefreshTokenHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthApiController
{
    public function __construct(
        private RegisterUserHandler $registerHandler,
        private LoginUserHandler $loginHandler,
        private RefreshTokenHandler $refreshHandler
    ) {}

    #[Route('/api/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new RegisterUserCommand(
            email: $data['email'] ?? '',
            password: $data['password'] ?? ''
        );

        $user = $this->registerHandler->handle($command);

        return new JsonResponse([
            'id' => $user->id,
            'email' => $user->email->value(),
        ]);
    }

    #[Route('/api/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new LoginUserCommand(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            deviceId: $data['deviceId'] ?? 'unknown'
        );

        $tokenPair = $this->loginHandler->handle($command);

        return new JsonResponse([
            'accessToken' => $tokenPair->accessToken,
            'refreshToken' => $tokenPair->refreshToken,
        ]);
    }

    #[Route('/api/auth/refresh', name: 'api_auth_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new RefreshTokenCommand(
            refreshToken: $data['refreshToken'] ?? '',
            deviceId: $data['deviceId'] ?? 'unknown'
        );

        $tokenPair = $this->refreshHandler->handle($command);

        return new JsonResponse([
            'accessToken' => $tokenPair->accessToken,
            'refreshToken' => $tokenPair->refreshToken,
        ]);
    }
}

