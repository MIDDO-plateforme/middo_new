<?php

namespace App\Infrastructure\IA\Provider;

use App\Domain\IA\Service\IAProviderInterface;
use App\Domain\IA\Service\TokenCounterInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NLLBProvider implements IAProviderInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiUrl,
        private TokenCounterInterface $counter
    ) {}

    public function generate(IARequest $request): IAResponse
    {
        // NLLB est souvent auto-hébergé (Ollama, HF Inference, serveur perso)
        $response = $this->client->request('POST', $this->apiUrl, [
            'json' => [
                'text' => $request->prompt,
                'target_lang' => $request->settings->targetLanguage ?? 'fr',
            ],
        ]);

        $data = $response->toArray();
        $text = $data['translation'] ?? '';

        $promptTokens = $this->counter->count($request->prompt);
        $responseTokens = $this->counter->count($text);

        return new IAResponse($text, $promptTokens + $responseTokens);
    }
}
