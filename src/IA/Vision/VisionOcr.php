<?php

namespace App\IA\Vision;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class VisionOcr
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
    ) {
    }

    public function extractText(string $imageBase64): string
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/images/ocr', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'image' => $imageBase64,
                ],
            ]);

            $data = $response->toArray(false);

            return $data['text'] ?? '';
        } catch (\Throwable $e) {
            return 'OCR Error: ' . $e->getMessage();
        }
    }
}
