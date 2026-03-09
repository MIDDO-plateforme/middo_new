<?php

namespace App\Interface\Http\Controller\Auth;

use App\Application\Auth\Command\LoginUserCommand;
use App\Application\Auth\Handler\LoginUserHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController
{
    public function __construct(
        private LoginUserHandler $handler
    ) {}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Missing credentials'], 400);
        }

        try {
            $command = new LoginUserCommand(
                email: $data['email'],
                password: $data['password'],
                deviceId: $data['deviceId'] ?? null
            );

            $tokenPair = ($this->handler)($command);

            return new JsonResponse([
                'token' => $tokenPair->accessToken,
                'refresh_token' => $tokenPair->refreshToken
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Invalid credentials',
                'details' => $e->getMessage()
            ], 401);
        }
    }
}
