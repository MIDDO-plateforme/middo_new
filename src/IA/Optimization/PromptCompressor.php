<?php

namespace App\IA\Optimization;

class PromptCompressor
{
    public function compress(string $prompt): string
    {
        // Nettoyage espaces
        $prompt = preg_replace('/\s+/', ' ', $prompt ?? '');
        $prompt = trim($prompt);

        // (Option simple) on limite la longueur max
        if (mb_strlen($prompt) > 4000) {
            $prompt = mb_substr($prompt, 0, 4000).' [...]';
        }

        return $prompt;
    }
}
