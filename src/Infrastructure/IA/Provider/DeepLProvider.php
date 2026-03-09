<?php

namespace App\Infrastructure\IA\Provider;

use App\Domain\IA\Service\IAProviderInterface;
use App\Domain\IA\Service\TokenCounterInterface;
use App\Domain\IA\ValueObject\IARequest;
use App\Domain\IA\ValueObject\IAResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeepLProvider implements IAProviderInterface
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiKey,
        private TokenCounterInterface $counter
    ) {}

    public function generate(IARequest $request): IAResponse
    {
        $response = $this->client->request('POST', 'https://api-free.deepl.com/v2/translate', [
            'headers' => [
                'Authorization' => 'DeepL-Auth-Key ' . $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'text' => $request->prompt,
                'target_lang' => strtoupper($request->settings->targetLanguage ?? 'FR'),
            ],
        ]);

        $data = $response->toArray();
        $text = $data['translations'][0]['text'] ?? '';

        $promptTokens = $this->counter->count($request->prompt);
        $responseTokens = $this->counter->count($text);

        return new IAResponse($text, $promptTokens + $responseTokens);
    }
}
