<?php

namespace App\Domain\User\Event;

class UserRegistered
{
    public function __construct(
        public readonly string $userId,
        public readonly string $email
    ) {}
}
