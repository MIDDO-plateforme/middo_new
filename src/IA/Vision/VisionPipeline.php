<?php

namespace App\IA\Vision;

class VisionPipeline
{
    public function __construct(
        private VisionProcessor $processor,
    ) {
    }

    public function run(string $imageBase64): array
    {
        return $this->processor->process($imageBase64);
    }
}
