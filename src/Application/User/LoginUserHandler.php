<?php

namespace App\Application\User;

use App\Application\User\LoginUser\LoginUserCommand;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\Auth\Service\JwtTokenGeneratorInterface;
use App\Domain\Auth\Service\RefreshTokenStoreInterface;
use App\Domain\User\ValueObject\Email;
use DateTimeImmutable;

class LoginUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $hasher,
        private JwtTokenGeneratorInterface $jwt,
        private RefreshTokenStoreInterface $refreshStore
    ) {}

    public function handle(LoginUserCommand $command)
    {
        $email = new Email($command->email);

        $user = $this->users->findByEmail($email);

        if (!$user) {
            throw new \Exception("Invalid credentials");
        }

        // 🔥 Correction définitive
        if (!$this->hasher->verify($user->getPassword(), $command->password)) {
            throw new \Exception("Invalid credentials");
        }

        $sessionId = uuid_create(UUID_TYPE_RANDOM);
        $deviceId = $command->deviceId ?? uuid_create(UUID_TYPE_RANDOM);

        $tokenPair = $this->jwt->generateTokenPair(
            $user,
            $sessionId,
            $deviceId
        );

        $this->refreshStore->store(
            $user,
            $sessionId,
            $deviceId,
            $tokenPair->refreshToken,
            new DateTimeImmutable('+30 days')
        );

        return $tokenPair;
    }
}
