<?php

namespace App\IA\Client;

class DummyIaClient implements IaClientInterface
{
    public function generate(string $prompt, array $options = []): string
    {
        return sprintf(
            "IA (dummy) — Résumé du prompt (%d caractères). À remplacer par ton moteur IA.",
            mb_strlen($prompt)
        );
    }
}
