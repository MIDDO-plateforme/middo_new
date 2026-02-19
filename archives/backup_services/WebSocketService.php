<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;

class WebSocketService
{
    private string $mercureHubUrl;
    private string $mercureJwtSecret;
    private ?LoggerInterface $logger;
    private array $connectedUsers = [];

    public function __construct(
        string $mercureHubUrl,
        string $mercureJwtSecret,
        ?LoggerInterface $logger = null
    ) {
        $this->mercureHubUrl = $mercureHubUrl;
        $this->mercureJwtSecret = $mercureJwtSecret;
        $this->logger = $logger;
    }

    public function publish(string $topic, array $data): bool
    {
        try {
            $client = HttpClient::create();
            $response = $client->request('POST', $this->mercureHubUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->generateJwt(),
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'topic' => $topic,
                    'data' => json_encode($data),
                ]),
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            $this->logger?->error('WebSocket publish failed', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function userConnected(int $userId, array $metadata = []): void
    {
        $this->connectedUsers[$userId] = [
            'user_id' => $userId,
            'connected_at' => new \DateTime(),
            'last_activity' => new \DateTime(),
            'metadata' => $metadata,
        ];

        $this->publish("presence", [
            'type' => 'user_connected',
            'user_id' => $userId,
            'timestamp' => (new \DateTime())->format('c'),
            'metadata' => $metadata,
        ]);
    }

    public function userDisconnected(int $userId): void
    {
        if (isset($this->connectedUsers[$userId])) {
            unset($this->connectedUsers[$userId]);

            $this->publish("presence", [
                'type' => 'user_disconnected',
                'user_id' => $userId,
                'timestamp' => (new \DateTime())->format('c'),
            ]);
        }
    }

    public function heartbeat(int $userId): void
    {
        if (isset($this->connectedUsers[$userId])) {
            $this->connectedUsers[$userId]['last_activity'] = new \DateTime();
        }
    }

    public function getConnectedUsers(): array
    {
        return array_values($this->connectedUsers);
    }

    public function isUserConnected(int $userId): bool
    {
        return isset($this->connectedUsers[$userId]);
    }

    public function pushNotification(int $userId, array $notification): bool
    {
        return $this->publish("notification/{$userId}", [
            'type' => 'notification',
            'user_id' => $userId,
            'data' => $notification,
            'timestamp' => (new \DateTime())->format('c'),
        ]);
    }

    private function generateJwt(): string
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'mercure' => ['publish' => ['*']],
            'exp' => time() + 3600,
        ]));
        $signature = hash_hmac('sha256', "$header.$payload", $this->mercureJwtSecret, true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }
}