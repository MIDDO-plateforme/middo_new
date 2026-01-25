<?php

namespace App\Service\AutoResolve;

use Doctrine\DBAL\Connection;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HealthCheckService
{
    private Connection $connection;
    private HttpClientInterface $httpClient;

    public function __construct(
        Connection $connection,
        HttpClientInterface $httpClient
    ) {
        $this->connection = $connection;
        $this->httpClient = $httpClient;
    }

    public function checkApiEndpoint(string $url, string $method = 'GET', ?array $payload = null): array
    {
        try {
            $options = [];
            if ($payload !== null) {
                $options['json'] = $payload;
            }

            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();

            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'status' => 'OK',
                    'code'   => $statusCode,
                ];
            }

            return [
                'status' => 'ERROR',
                'code'   => $statusCode,
                'error'  => 'Unexpected status code',
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'ERROR',
                'code'   => null,
                'error'  => $e->getMessage(),
            ];
        }
    }

    public function checkDatabaseConnection(): array
    {
        try {
            $this->connection->executeQuery('SELECT 1');

            return [
                'status' => 'OK',
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'ERROR',
                'error'  => $e->getMessage(),
            ];
        }
    }

    public function checkEnvironmentVariables(): array
    {
        $required = [
            'APP_ENV',
            'APP_SECRET',
            'DATABASE_URL',
            'JWT_SECRET',
            'JWT_SECRET_KEY',
        ];

        $missing = [];
        foreach ($required as $name) {
            $value = $_ENV[$name] ?? $_SERVER[$name] ?? null;
            if ($value === null || $value === '') {
                $missing[] = $name;
            }
        }

        if ($missing === []) {
            return [
                'status' => 'OK',
            ];
        }

        return [
            'status'  => 'ERROR',
            'missing' => $missing,
        ];
    }

    public function runAll(): array
    {
        $baseUrl = $_ENV['MIDDO_BASE_URL'] ?? $_SERVER['MIDDO_BASE_URL'] ?? null;

        $apiResults = [];

        if ($baseUrl !== null) {
            $apiResults['projets_public'] = $this->checkApiEndpoint(rtrim($baseUrl, '/') . '/projets-public', 'GET');
            $apiResults['login_check'] = $this->checkApiEndpoint(rtrim($baseUrl, '/') . '/api/login_check', 'POST', [
                'username' => 'dummy@example.com',
                'password' => 'dummy',
            ]);
        }

        return [
            'api'      => $apiResults,
            'database' => $this->checkDatabaseConnection(),
            'env'      => $this->checkEnvironmentVariables(),
        ];
    }
}
