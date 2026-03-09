<?php

namespace App\Infrastructure\IA\Provider;

class ConsistencyCheckProvider
{
    /**
     * Analyse un texte et renvoie une estimation de cohérence.
     */
    public function analyze(string $text): array
    {
        $length = mb_strlen($text);

        if ($length < 20) {
            return [
                'consistency' => 0.3,
                'notes' => ['Texte très court, difficile à analyser.'],
            ];
        }

        $sentences = preg_split('/[.!?]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);

        $consistency = 0.5;
        $notes = [];

        if ($sentenceCount > 1) {
            $consistency += 0.2;
        }

        if ($length > 200) {
            $consistency += 0.1;
        }

        $consistency = min(1.0, $consistency);

        return [
            'consistency' => $consistency,
            'notes' => $notes,
        ];
    }
}
