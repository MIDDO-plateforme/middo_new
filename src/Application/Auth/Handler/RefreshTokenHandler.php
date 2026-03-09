<?php

namespace App\Application\Auth\Handler;

use App\Application\Auth\Command\RefreshTokenCommand;
use App\Domain\Auth\Service\JwtTokenGeneratorInterface;
use App\Domain\Auth\Service\RefreshTokenStoreInterface;

class RefreshTokenHandler
{
    public function __construct(
        private JwtTokenGeneratorInterface $jwt,
        private RefreshTokenStoreInterface $refreshTokens
    ) {}

    public function __invoke(RefreshTokenCommand $command)
    {
        $userId = $this->refreshTokens->validate(
            $command->refreshToken,
            $command->deviceId
        );

        $tokenPair = $this->jwt->generateTokensFromUserId($userId, $command->deviceId);

        $this->refreshTokens->store(
            $userId,
            $tokenPair->refreshToken,
            $command->deviceId
        );

        return $tokenPair;
    }
}
