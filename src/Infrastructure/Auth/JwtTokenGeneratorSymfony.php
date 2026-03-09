<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Service\JwtTokenGeneratorInterface;
use App\Domain\Auth\ValueObject\TokenPair;
use App\Domain\User\Entity\User;
use Firebase\JWT\JWT;

class JwtTokenGeneratorSymfony implements JwtTokenGeneratorInterface
{
    public function __construct(
        private string $jwtSecret,
        private string $jwtIssuer
    ) {}

    public function generate(User $user): TokenPair
    {
        $now = time();

        $payload = [
            'sub' => $user->id(),
            'email' => $user->email()->value(),
            'iat' => $now,
            'exp' => $now + 3600,
            'iss' => $this->jwtIssuer,
        ];

        $accessToken = JWT::encode($payload, $this->jwtSecret, 'HS256');

        // Le refresh token sera injecté par LoginUserHandler
        return new TokenPair($accessToken, '');
    }
}
