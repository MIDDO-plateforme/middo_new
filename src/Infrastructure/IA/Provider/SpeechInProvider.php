<?php

namespace App\Infrastructure\IA\Provider;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpeechInProvider
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiUrl,
        private string $apiKey
    ) {}

    public function transcribe(string $audioContent): string
    {
        // audioContent : base64 ou binaire selon ton choix
        $response = $this->client->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
            'body' => $audioContent,
        ]);

        $data = $response->toArray();

        return $data['text'] ?? '';
    }
}
