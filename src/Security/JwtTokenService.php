<?php

namespace App\Security;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenService
{
    public function __construct(
        private string $jwtSecret,
        private int $jwtTtl
    ) {}

    public function createToken(User $user): string
    {
        $now = time();
        $payload = [
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'iat' => $now,
            'exp' => $now + $this->jwtTtl,
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function decodeToken(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
        return (array) $decoded;
    }
}
