<?php

namespace App\IA\Memory;

use App\IA\AiKernel;

class EmbeddingProvider
{
    public function __construct(
        private AiKernel $kernel
    ) {}

    /**
     * @return array<int, float>
     */
    public function embed(string $text): array
    {
        $prompt = <<<PROMPT
Tu es un générateur d'empreintes numériques (embeddings).

Entrée :
- Un texte

Ta mission :
- Produire un tableau JSON de nombres (floats) représentant ce texte.
- Le format doit être STRICTEMENT un tableau JSON, par exemple :
[0.12, -0.34, 0.56]

Texte :
$text
PROMPT;

        $json = $this->kernel->generate($prompt);
        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            return [];
        }

        $vector = [];
        foreach ($decoded as $value) {
            if (is_numeric($value)) {
                $vector[] = (float) $value;
            }
        }

        return $vector;
    }
}
