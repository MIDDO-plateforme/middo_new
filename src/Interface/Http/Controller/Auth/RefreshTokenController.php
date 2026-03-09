<?php

namespace App\Interface\Http\Controller\Auth;

use App\Domain\Auth\Service\RefreshTokenStoreInterface;
use App\Domain\Auth\Service\JwtTokenGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RefreshTokenController
{
    public function __construct(
        private RefreshTokenStoreInterface $refreshTokenStore,
        private JwtTokenGeneratorInterface $jwtTokenGenerator
    ) {}

    #[Route('/api/refresh', name: 'api_refresh', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $refreshTokenId = $request->request->get('refresh_token');

        if (!$refreshTokenId) {
            return new JsonResponse(['error' => 'Missing refresh token'], 400);
        }

        $token = $this->refreshTokenStore->find($refreshTokenId);

        if (!$token || $token->isExpired()) {
            return new JsonResponse(['error' => 'Invalid or expired refresh token'], 401);
        }

        $user = $token->getUser();

        // Nouveau JWT
        $jwt = $this->jwtTokenGenerator->generate($user);

        // Rotation du refresh token
        $newToken = $this->refreshTokenStore->rotate($token);

        return new JsonResponse([
            'token' => $jwt,
            'refresh_token' => $newToken->getId(),
        ]);
    }
}
