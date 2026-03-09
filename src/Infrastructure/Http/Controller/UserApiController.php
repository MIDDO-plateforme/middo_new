<?php

namespace App\Infrastructure\Http\Controller;

use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserApiController
{
    public function __construct(
        private UserRepositoryInterface $users
    ) {}

    #[Route('/api/user/me', name: 'api_user_me', methods: ['GET'])]
    public function me(#[CurrentUser] ?string $userId): JsonResponse
    {
        if (!$userId) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $user = $this->users->findById($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->id,
            'email' => $user->email->value(),
            'iaSettings' => [
                'tone' => $user->iaSettings->tone,
                'temperature' => $user->iaSettings->temperature,
                'maxTokens' => $user->iaSettings->maxTokens,
            ],
        ]);
    }
}
