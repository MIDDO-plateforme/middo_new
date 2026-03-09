<?php

namespace App\IA\Vision;

use App\IA\Client\IaClientInterface;

class VisionAnalyzer
{
    public function __construct(
        private IaClientInterface $iaClient,
    ) {
    }

    public function analyze(string $text): string
    {
        return $this->iaClient->generate(
            "Analyse ce texte extrait d'une image :\n\n" . $text,
            ['target' => 'openai']
        );
    }
}
