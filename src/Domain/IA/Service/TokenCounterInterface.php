<?php

namespace App\Domain\IA\Service;

interface TokenCounterInterface
{
    public function count(string $text): int;
}
