<?php

namespace App\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class AdminControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        
        // Créer et authentifier un utilisateur admin pour les tests
        $container = static::getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        
        // Créer un utilisateur admin de test
        $adminUser = $this->createAdminUser($entityManager);
        
        // Simuler la connexion de l'utilisateur admin
        $this->client->loginUser($adminUser);
    }

    private function createAdminUser($entityManager): User
    {
        // Chercher ou créer un utilisateur admin
        $userRepository = $entityManager->getRepository(User::class);
        $adminUser = $userRepository->findOneBy(['email' => 'admin@test.com']);
        
        if (!$adminUser) {
            $adminUser = new User();
            $adminUser->setEmail('admin@test.com');
            $adminUser->setRoles(['ROLE_ADMIN']);
            $adminUser->setPassword('$2y$13$hashedpassword'); // Password fictif pour les tests
            
            $entityManager->persist($adminUser);
            $entityManager->flush();
        }
        
        return $adminUser;
    }

    public function testDashboardPageAccessible(): void
    {
        $this->client->request('GET', '/admin/dashboard');
        $this->assertResponseIsSuccessful();
    }

    public function testApiStatsEndpoint(): void
    {
        $this->client->request('GET', '/admin/api/stats');
        $this->assertResponseIsSuccessful();
        
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('openai', $data);
        $this->assertArrayHasKey('anthropic', $data);
    }

    public function testElasticsearchStatsEndpoint(): void
    {
        $this->client->request('GET', '/admin/api/elasticsearch/stats');
        $this->assertResponseIsSuccessful();
        
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('status', $data);
    }

    public function testRedisStatsEndpoint(): void
    {
        $this->client->request('GET', '/admin/api/redis/stats');
        $this->assertResponseIsSuccessful();
        
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('status', $data);
    }

    public function testSystemStatsEndpoint(): void
    {
        $this->client->request('GET', '/admin/api/system/stats');
        $this->assertResponseIsSuccessful();
        
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('php_version', $data);
        $this->assertArrayHasKey('memory_usage', $data);
    }

    public function testClearCacheAction(): void
    {
        $this->client->request('POST', '/admin/cache/clear');
        $this->assertResponseIsSuccessful();
        
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('success', $data);
    }

    public function testReindexElasticsearchAction(): void
    {
        $this->client->request('POST', '/admin/elasticsearch/reindex');
        $this->assertResponseIsSuccessful();
        
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('success', $data);
    }

    public function testLogsPageAccessible(): void
    {
        $this->client->request('GET', '/admin/logs');
        $this->assertResponseIsSuccessful();
    }

    public function testWrongHttpMethodsReturnError(): void
    {
        // Test POST sur une route GET
        $this->client->request('POST', '/admin/dashboard');
        $this->assertResponseStatusCodeSame(405);
        
        // Test GET sur une route POST
        $this->client->request('GET', '/admin/cache/clear');
        $this->assertResponseStatusCodeSame(405);
    }

    public function testAllApiRoutesReturnJsonContentType(): void
    {
        $apiRoutes = [
            '/admin/api/stats',
            '/admin/api/elasticsearch/stats',
            '/admin/api/redis/stats',
            '/admin/api/system/stats'
        ];
        
        foreach ($apiRoutes as $route) {
            $this->client->request('GET', $route);
            $this->assertResponseHeaderSame('Content-Type', 'application/json');
        }
    }

    public function testApiResponseTimeUnder10Seconds(): void
    {
        $startTime = microtime(true);
        $this->client->request('GET', '/admin/api/stats');
        $endTime = microtime(true);
        
        $responseTime = $endTime - $startTime;
        $this->assertLessThan(10, $responseTime, 'API response time exceeded 10 seconds');
    }

    public function testResilientToUnavailableServices(): void
    {
        // Même si Elasticsearch ou Redis sont down, l'admin dashboard doit rester accessible
        $this->client->request('GET', '/admin/dashboard');
        $this->assertResponseIsSuccessful();
        
        $this->client->request('GET', '/admin/api/elasticsearch/stats');
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        
        // Si Elasticsearch est down, le status doit être 'unavailable' mais pas d'erreur 500
        $this->assertTrue(
            $data['status'] === 'unavailable' || isset($data['nodes']),
            'Elasticsearch stats should handle unavailable service gracefully'
        );
    }

    public function testAllAdminRoutesExist(): void
    {
        $routes = [
            ['GET', '/admin/dashboard'],
            ['GET', '/admin/api/stats'],
            ['GET', '/admin/api/elasticsearch/stats'],
            ['GET', '/admin/api/redis/stats'],
            ['GET', '/admin/api/system/stats'],
            ['GET', '/admin/logs']
        ];
        
        foreach ($routes as [$method, $path]) {
            $this->client->request($method, $path);
            $this->assertResponseIsSuccessful(
                sprintf('Route %s %s should be accessible', $method, $path)
            );
        }
        
        $postRoutes = [
            '/admin/cache/clear',
            '/admin/elasticsearch/reindex'
        ];
        
        foreach ($postRoutes as $path) {
            $this->client->request('POST', $path);
            $this->assertResponseIsSuccessful(
                sprintf('Route POST %s should be accessible', $path)
            );
        }
    }
}
