<?php

namespace App\Infrastructure\IA\Provider;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpeechOutProvider
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiUrl,
        private string $apiKey
    ) {}

    public function synthesize(string $text, string $language): string
    {
        $response = $this->client->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'text' => $text,
                'language' => $language,
            ],
        ]);

        $data = $response->toArray();

        // Retour : audio encodé (ex: base64)
        return $data['audio'] ?? '';
    }
}
