<?php

namespace App\IA\Pipeline;

use App\IA\AiKernel;
use App\AI\DTO\AIResponse;

final class UserPipeline
{
    public function __construct(
        private readonly AiKernel $kernel
    ) {}

    public function summarizeProfile(array $userData, string $model = 'gpt-4o'): AIResponse
    {
        $json = json_encode($userData, JSON_PRETTY_PRINT);

        $prompt = <<<TXT
Voici les données d'un utilisateur :

$json

Génère un résumé clair et utile :
- profil
- besoins
- risques
- recommandations
TXT;

        return $this->kernel->askBest($prompt, $model, 'analysis');
    }
}
