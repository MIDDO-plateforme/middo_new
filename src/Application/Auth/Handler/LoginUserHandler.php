<?php

namespace App\Application\Auth\Handler;

use App\Application\Auth\Command\LoginUserCommand;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\Auth\Service\JwtTokenGeneratorInterface;
use App\Domain\Auth\Service\RefreshTokenStoreInterface;
use App\Domain\User\ValueObject\Email;
use App\Domain\Auth\ValueObject\TokenPair;

class LoginUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $hasher,
        private JwtTokenGeneratorInterface $jwt,
        private RefreshTokenStoreInterface $refreshTokens
    ) {}

    public function __invoke(LoginUserCommand $command): TokenPair
    {
        $email = new Email($command->email);

        $user = $this->users->findByEmail($email);

        if (!$user || !$this->hasher->verify($user->getPassword(), $command->password)) {
            throw new \RuntimeException('Invalid credentials');
        }

        $tokenPair = $this->jwt->generate($user);

        $refreshToken = $this->refreshTokens->create($user);

        return new TokenPair(
            accessToken: $tokenPair->accessToken,
            refreshToken: $refreshToken->getId()
        );
    }
}
