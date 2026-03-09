<?php

namespace App\Domain\Auth\Service;

use App\Domain\User\Entity\User;
use App\Domain\Auth\ValueObject\TokenPair;

interface JwtTokenGeneratorInterface
{
    public function generate(User $user): TokenPair;
}
