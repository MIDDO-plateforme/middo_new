<?php

namespace App\Infrastructure\IA\Service;

use App\Domain\IA\Service\TokenCounterInterface;

class TokenCounterSi implements TokenCounterInterface
{
    public function count(string $text): int
    {
        // Version simple : 1 token ≈ 4 caractères
        return (int) ceil(strlen($text) / 4);
    }
}
