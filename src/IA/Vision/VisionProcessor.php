<?php

namespace App\IA\Vision;

class VisionProcessor
{
    public function __construct(
        private VisionOcr $ocr,
        private VisionAnalyzer $analyzer,
    ) {
    }

    public function process(string $imageBase64): array
    {
        $text = $this->ocr->extractText($imageBase64);
        $analysis = $this->analyzer->analyze($text);

        return [
            'text' => $text,
            'analysis' => $analysis,
        ];
    }
}
