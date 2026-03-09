<?php

namespace App\IA\Security;

class IAInputSanitizer
{
    public function sanitize(string $prompt): string
    {
        // Empêche les tentatives de jailbreak
        $blocked = [
            'ignore previous instructions',
            'system prompt',
            'you are now',
            'pretend to be',
            'bypass',
            'jailbreak',
            'developer mode',
        ];

        foreach ($blocked as $word) {
            if (stripos($prompt, $word) !== false) {
                throw new \RuntimeException('Prompt interdit');
            }
        }

        return trim($prompt);
    }
}
