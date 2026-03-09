<?php

namespace App\Infrastructure\Auth\Jwt;

use App\Domain\Auth\Service\JwtTokenGeneratorInterface;
use App\Domain\Auth\ValueObject\TokenPair;
use App\Domain\User\Entity\User;

class JwtTokenGenerator implements JwtTokenGeneratorInterface
{
    public function __construct(
        private string $secret = 'CHANGE_ME'
    ) {}

    public function generate(User $user): TokenPair
    {
        $now = time();

        $payload = [
            'sub' => $user->id(),
            'email' => $user->email()->value(),
            'iat' => $now,
            'exp' => $now + 3600
        ];

        $accessToken = $this->encode($payload);

        // Le refresh token sera injecté par LoginUserHandler
        return new TokenPair($accessToken, '');
    }

    private function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload))
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $this->secret, true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
