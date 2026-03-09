<?php

namespace App\Integrations\Drivers;

class DemoDriver extends BaseDriver
{
    public function getName(): string
    {
        return 'demo';
    }

    public function connect(array $config = []): bool
    {
        return true; // Always OK for demo
    }

    public function send(array $payload): array
    {
        return [
            'status' => 'success',
            'echo' => $payload,
        ];
    }
}
