<?php

namespace App\Interface\Http\Controller;

use App\Application\Auth\Command\RegisterUserCommand;
use App\Application\Auth\Command\LoginUserCommand;
use App\Application\Auth\Command\RefreshTokenCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class AuthApiController
{
    public function __construct(private MessageBusInterface $bus) {}

    #[Route('/api/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new RegisterUserCommand(
            email: $data['email'] ?? '',
            password: $data['password'] ?? ''
        );

        $envelope = $this->bus->dispatch($command);
        $handled = $envelope->last(HandledStamp::class);
        $userId = $handled->getResult();

        return new JsonResponse([
            'status' => 'ok',
            'userId' => $userId
        ]);
    }

    #[Route('/api/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $command = new LoginUserCommand(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            deviceId: $data['deviceId'] ?? null
        );

        $envelope = $this->bus->dispatch($command);
        $handled = $envelope->last(HandledStamp::class);
        $tokenPair = $handled->getResult();

        return new JsonResponse([
            'status' => 'ok',
            'accessToken' => $tokenPair->accessToken,
            'refreshToken' => $tokenPair->refreshToken
        ]);
    }

    #[Route('/api/auth/refresh', name: 'api_auth_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new RefreshTokenCommand(
            refreshToken: $data['refreshToken'] ?? '',
            deviceId: $data['deviceId'] ?? null
        );

        $envelope = $this->bus->dispatch($command);
        $handled = $envelope->last(HandledStamp::class);
        $tokenPair = $handled->getResult();

        return new JsonResponse([
            'status' => 'ok',
            'accessToken' => $tokenPair->accessToken,
            'refreshToken' => $tokenPair->refreshToken
        ]);
    }
}
