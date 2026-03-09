<?php

namespace App\Integrations\Drivers;

abstract class BaseDriver
{
    abstract public function getName(): string;

    abstract public function connect(array $config = []): bool;

    abstract public function send(array $payload): array;
}
