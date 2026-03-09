<?php

namespace App\Service\AI;

class SentimentAnalysisService
{
    public function analyze(string $text): array
    {
        return [
            'score' => 0.0,
            'label' => 'neutral',
        ];
    }
}
