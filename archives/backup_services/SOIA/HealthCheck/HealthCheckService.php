<?php

namespace App\Service\SOIA\HealthCheck;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HealthCheckService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private array $cache = [];
    private const CACHE_TTL = 60; // 60 secondes

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function checkApi(string $apiName, string $endpoint): array
    {
        $cacheKey = "health_{$apiName}";
        
        if (isset($this->cache[$cacheKey]) && 
            time() - $this->cache[$cacheKey]['timestamp'] < self::CACHE_TTL) {
            return $this->cache[$cacheKey]['data'];
        }

        $startTime = microtime(true);
        
        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'timeout' => 5,
                'max_duration' => 5
            ]);
            
            $statusCode = $response->getStatusCode();
            $duration = (microtime(true) - $startTime) * 1000;
            
            $result = [
                'status' => $statusCode >= 200 && $statusCode < 300 ? 'up' : 'down',
                'response_time' => round($duration, 2),
                'status_code' => $statusCode,
                'timestamp' => time()
            ];
            
            $this->logger->info("HealthCheck [{$apiName}]: {$result['status']}", [
                'duration' => $result['response_time']
            ]);
            
        } catch (\Exception $e) {
            $result = [
                'status' => 'down',
                'error' => $e->getMessage(),
                'timestamp' => time()
            ];
            
            $this->logger->error("HealthCheck [{$apiName}] FAILED", [
                'error' => $e->getMessage()
            ]);
        }
        
        $this->cache[$cacheKey] = [
            'data' => $result,
            'timestamp' => time()
        ];
        
        return $result;
    }

    public function checkAllApis(): array
    {
        return [
            'openai' => $this->checkApi('openai', 'https://api.openai.com/v1/models'),
            'anthropic' => $this->checkApi('anthropic', 'https://api.anthropic.com/v1/messages'),
        ];
    }

    public function getSystemStatus(): string
    {
        $apis = $this->checkAllApis();
        $allUp = array_reduce($apis, fn($carry, $api) => $carry && ($api['status'] === 'up'), true);
        
        return $allUp ? 'operational' : 'degraded';
    }
}