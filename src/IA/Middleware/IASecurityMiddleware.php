<?php

namespace App\IA\Middleware;

use Symfony\Component\Security\Core\Security;

class IASecurityMiddleware
{
    public function __construct(private Security $security) {}

    public function check(): void
    {
        if (!$this->security->getUser()) {
            throw new \RuntimeException("Accès IA non autorisé.");
        }
    }
}
