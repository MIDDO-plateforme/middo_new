<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HealthCheckService
{
    private LoggerInterface $logger;
    private ParameterBagInterface $params;
    private float $startTime;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $params)
    {
        $this->logger = $logger;
        $this->params = $params;
        $this->startTime = microtime(true);
    }

    public function getFullStatus(): array
    {
        $checks = [
            'environment' => $this->checkEnvironment(),
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'mercure' => $this->checkMercure(),
            'api_keys' => $this->checkApiKeys(),
            'system' => $this->checkSystem(),
            'latency' => $this->checkLatency(),
        ];

        return [
            'status' => $this->computeGlobalStatus($checks),
            'version' => $this->getAppVersion(),
            'php_version' => PHP_VERSION,
            'uptime_seconds' => $this->getUptime(),
            'checked_at' => date('c'),
            'details' => $checks,
        ];
    }

    private function computeGlobalStatus(array $checks): string
    {
        foreach ($checks as $check) {
            if (($check['status'] ?? 'OK') !== 'OK') {
                return 'DEGRADED';
            }
        }
        return 'OK';
    }

    private function checkEnvironment(): array
    {
        $required = [
            'APP_ENV',
            'APP_SECRET',
            'JWT_SECRET',
            'JWT_PASSPHRASE',
            'OPENAI_API_KEY',
            'ANTHROPIC_API_KEY',
            'CLAUDE_API_KEY',
            'MERCURE_JWT_SECRET',
        ];

        $missing = [];

        foreach ($required as $key) {
            if (!$this->params->has($key) && !getenv($key)) {
                $missing[] = $key;
            }
        }

        return [
            'status' => empty($missing) ? 'OK' : 'ERROR',
            'missing' => $missing,
        ];
    }

    private function checkDatabase(): array
    {
        try {
            $dbPath = __DIR__ . '/../../var/data.db';

            if (!file_exists($dbPath)) {
                return [
                    'status' => 'ERROR',
                    'message' => 'Database file not found: ' . $dbPath,
                ];
            }

            return ['status' => 'OK'];

        } catch (\Throwable $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            return [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkRedis(): array
    {
        try {
            $redisUrl = getenv('REDIS_URL');

            if (!$redisUrl || $redisUrl === 'redis://null') {
                return ['status' => 'SKIPPED', 'message' => 'Redis not configured'];
            }

            $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379, 0.5);

            return $redis->ping() ? ['status' => 'OK'] : ['status' => 'ERROR'];

        } catch (\Throwable $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function checkMercure(): array
    {
        try {
            $url = getenv('MERCURE_HUB_URL');

            if (!$url) {
                return ['status' => 'SKIPPED', 'message' => 'Mercure not configured'];
            }

            $context = stream_context_create(['http' => ['timeout' => 1]]);
            $result = @file_get_contents($url, false, $context);

            return $result !== false ? ['status' => 'OK'] : ['status' => 'ERROR'];

        } catch (\Throwable $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    private function checkApiKeys(): array
    {
        $keys = [
            'openai' => getenv('OPENAI_API_KEY'),
            'anthropic' => getenv('ANTHROPIC_API_KEY'),
            'claude' => getenv('CLAUDE_API_KEY'),
        ];

        $status = [];

        foreach ($keys as $name => $value) {
            $status[$name] = empty($value) ? 'MISSING' : 'OK';
        }

        return [
            'status' => in_array('MISSING', $status, true) ? 'ERROR' : 'OK',
            'providers' => $status,
        ];
    }

    private function checkSystem(): array
    {
        // CPU load (Windows + Linux safe)
        $cpuLoad = null;
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $cpuLoad = $load[0] ?? null;
        }

        $memory = round(memory_get_usage(true) / 1024 / 1024, 2);

        $disk = null;
        try {
            $disk = round(disk_free_space(__DIR__) / 1024 / 1024, 2);
        } catch (\Throwable $e) {
            $disk = null;
        }

        return [
            'status' => 'OK',
            'cpu_load' => $cpuLoad,
            'memory_usage_mb' => $memory,
            'disk_free_mb' => $disk,
        ];
    }

    private function checkLatency(): array
    {
        $start = microtime(true);
        usleep(5000); // 5ms
        $latency = microtime(true) - $start;

        return [
            'status' => 'OK',
            'latency_ms' => round($latency * 1000, 2),
        ];
    }

    private function getAppVersion(): string
    {
        return 'MIDDO v2.0';
    }

    public function getUptime(): int
    {
        return (int) (microtime(true) - $this->startTime);
    }
}
