<?php

namespace App\Infrastructure\Auth\Security;

use App\Domain\User\Repository\UserRepositoryInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private JwtSecret $jwtSecret,
        private UserRepositoryInterface $users
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $header = $request->headers->get('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            throw new AuthenticationException('Missing or invalid Authorization header.');
        }

        $token = substr($header, 7);

        try {
            $payload = JWT::decode($token, new Key($this->jwtSecret->value, 'HS256'));
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid token.');
        }

        return new SelfValidatingPassport(
            new UserBadge($payload->sub, function () use ($payload) {
                return $this->users->findById($payload->sub);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?\Symfony\Component\HttpFoundation\Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        return new JsonResponse([
            'error' => 'Unauthorized',
            'message' => $exception->getMessage()
        ], 401);
    }
}
