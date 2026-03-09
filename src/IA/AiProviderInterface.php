<?php

namespace App\IA;

interface AiProviderInterface
{
    public function getName(): string;

    /**
     * @param string $prompt
     * @param array<string,mixed> $options
     */
    public function chat(string $prompt, array $options = []): string;
}
