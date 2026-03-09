<?php

namespace App\Domain\IA\ValueObject;

class IAResponse
{
    public string $text;
    public int $tokensUsed;

    public function __construct(string $text, int $tokensUsed)
    {
        $this->text = $text;
        $this->tokensUsed = $tokensUsed;
    }
}
