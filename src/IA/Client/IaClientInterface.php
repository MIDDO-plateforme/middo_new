<?php

namespace App\IA\Client;

interface IaClientInterface
{
    /**
     * @param string $prompt  Prompt complet (contexte + instruction)
     * @param array  $options Options spécifiques (model, temperature, etc.)
     */
    public function generate(string $prompt, array $options = []): string;
}
