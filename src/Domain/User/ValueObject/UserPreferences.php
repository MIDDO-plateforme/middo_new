<?php

namespace App\Domain\User\ValueObject;

class UserPreferences
{
    public function __construct(
        public readonly bool $darkMode = false,
        public readonly bool $notifications = true,
        public readonly string $dashboardLayout = 'default'
    ) {}
}
