<?php

namespace App\Service\Ia;

class IaAssistantService
{
    public function generateResponse(string $message): array
    {
        return [
            'reply' => 'Réponse simulée pour : "' . $message . '"',
        ];
    }
}
