<?php

namespace App\Domain\Auth\Service;

use App\Domain\User\Entity\User;
use App\Domain\Auth\Entity\RefreshTokenEntity;

interface RefreshTokenStoreInterface
{
    public function create(User $user): RefreshTokenEntity;

    public function find(string $id): ?RefreshTokenEntity;

    public function rotate(RefreshTokenEntity $oldToken): RefreshTokenEntity;

    public function remove(RefreshTokenEntity $token): void;
}
