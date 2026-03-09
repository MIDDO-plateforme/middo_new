<?php

namespace App\IA\Client;

class MiddoCoreClient implements IaClientInterface
{
    public function generate(string $prompt, array $options = []): string
    {
        return "[MIDDO-CORE] " . $prompt;
    }
}
