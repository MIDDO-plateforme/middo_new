<?php

namespace App\Service\Elasticsearch;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ElasticsearchService
{
    private Client $client;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private string $indexName = 'users';
    private string $projectDir;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        ParameterBagInterface $params
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->projectDir = $params->get('kernel.project_dir');
        
        $elasticsearchUrl = $_ENV['ELASTICSEARCH_URL'] ?? 'http://localhost:9200';
        
        $this->client = ClientBuilder::create()
            ->setHosts([$elasticsearchUrl])
            ->build();
    }

    public function createUsersIndex(): bool
    {
        try {
            if ($this->indexExists()) {
                $this->logger->warning("Index '{$this->indexName}' existe déjà.");
                return false;
            }

            $mappingPath = $this->projectDir . '/config/elasticsearch/mapping_users.json';
            
            if (!file_exists($mappingPath)) {
                throw new \RuntimeException("Mapping file not found: {$mappingPath}");
            }

            $mappingContent = file_get_contents($mappingPath);
            $mapping = json_decode($mappingContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException("Invalid JSON: " . json_last_error_msg());
            }

            $params = [
                'index' => $this->indexName,
                'body' => $mapping
            ];

            $response = $this->client->indices()->create($params);

            $this->logger->info("Index créé avec succès");

            return true;

        } catch (\Exception $e) {
            $this->logger->error("Erreur création index: " . $e->getMessage());
            return false;
        }
    }

    public function indexUser(User $user): bool
    {
        try {
            if (!$this->indexExists()) {
                $this->logger->error("Index n'existe pas.");
                return false;
            }

            $document = $this->buildUserDocument($user);

            $params = [
                'index' => $this->indexName,
                'id' => $user->getId(),
                'body' => $document
            ];

            $this->client->index($params);

            return true;

        } catch (\Exception $e) {
            $this->logger->error("Erreur indexation: " . $e->getMessage());
            return false;
        }
    }

    public function bulkIndexUsers(array $users, int $batchSize = 500): array
    {
        $stats = ['success' => 0, 'failed' => 0, 'errors' => []];

        try {
            if (!$this->indexExists()) {
                throw new \RuntimeException("Index n'existe pas.");
            }

            $batches = array_chunk($users, $batchSize);

            foreach ($batches as $batch) {
                $params = ['body' => []];

                foreach ($batch as $user) {
                    if (!$user instanceof User) {
                        $stats['failed']++;
                        continue;
                    }

                    $params['body'][] = [
                        'index' => [
                            '_index' => $this->indexName,
                            '_id' => $user->getId()
                        ]
                    ];

                    $params['body'][] = $this->buildUserDocument($user);
                }

                if (!empty($params['body'])) {
                    $response = $this->client->bulk($params);

                    if (isset($response['items'])) {
                        foreach ($response['items'] as $item) {
                            if (isset($item['index']['status']) && ($item['index']['status'] === 201 || $item['index']['status'] === 200)) {
                                $stats['success']++;
                            } else {
                                $stats['failed']++;
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->logger->error("Erreur bulk: " . $e->getMessage());
            $stats['errors'][] = $e->getMessage();
        }

        return $stats;
    }

    public function searchUsers(array $criteria): array
    {
        try {
            $params = [
                'index' => $this->indexName,
                'body' => [
                    'from' => $criteria['from'] ?? 0,
                    'size' => $criteria['size'] ?? 20,
                    'query' => $this->buildSearchQuery($criteria),
                    'aggs' => $this->buildAggregations(),
                    'sort' => [
                        '_score' => ['order' => 'desc']
                    ]
                ]
            ];

            $response = $this->client->search($params);

            return [
                'hits' => $this->formatHits($response['hits']['hits'] ?? []),
                'total' => $response['hits']['total']['value'] ?? 0,
                'facets' => $this->formatAggregations($response['aggregations'] ?? [])
            ];

        } catch (\Exception $e) {
            $this->logger->error("Erreur recherche: " . $e->getMessage());
            return ['hits' => [], 'total' => 0, 'facets' => []];
        }
    }

    public function autocomplete(string $query, int $limit = 10): array
    {
        try {
            $params = [
                'index' => $this->indexName,
                'body' => [
                    'size' => $limit,
                    'query' => [
                        'bool' => [
                            'should' => [
                                ['match' => ['first_name' => ['query' => $query, 'boost' => 3, 'fuzziness' => 'AUTO']]],
                                ['match' => ['last_name' => ['query' => $query, 'boost' => 3, 'fuzziness' => 'AUTO']]],
                                ['match_phrase_prefix' => ['email' => ['query' => $query, 'boost' => 2]]]
                            ]
                        ]
                    ],
                    '_source' => ['id', 'first_name', 'last_name', 'email', 'profile_picture']
                ]
            ];

            $response = $this->client->search($params);

            return array_map(function($hit) {
                $source = $hit['_source'];
                return [
                    'id' => $source['id'],
                    'name' => trim(($source['first_name'] ?? '') . ' ' . ($source['last_name'] ?? '')),
                    'email' => $source['email'] ?? '',
                    'avatar' => $source['profile_picture'] ?? null
                ];
            }, $response['hits']['hits'] ?? []);

        } catch (\Exception $e) {
            $this->logger->error("Erreur autocomplete: " . $e->getMessage());
            return [];
        }
    }

    public function deleteUser(int $userId): bool
    {
        try {
            $params = ['index' => $this->indexName, 'id' => $userId];
            $this->client->delete($params);
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Erreur suppression: " . $e->getMessage());
            return false;
        }
    }

    public function reindexAll(): array
    {
        try {
            if ($this->indexExists()) {
                $this->deleteIndex();
            }

            $this->createUsersIndex();
            $users = $this->em->getRepository(User::class)->findAll();
            $stats = $this->bulkIndexUsers($users);

            return $stats;

        } catch (\Exception $e) {
            $this->logger->error("Erreur réindexation: " . $e->getMessage());
            return ['success' => 0, 'failed' => 0, 'errors' => [$e->getMessage()]];
        }
    }

    public function indexExists(): bool
    {
        try {
            return $this->client->indices()->exists(['index' => $this->indexName])->asBool();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteIndex(): bool
    {
        try {
            if ($this->indexExists()) {
                $this->client->indices()->delete(['index' => $this->indexName]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function buildUserDocument(User $user): array
    {
        $skills = [];
        if (method_exists($user, 'getSkills')) {
            foreach ($user->getSkills() as $skill) {
                $skills[] = method_exists($skill, 'getName') ? $skill->getName() : (string)$skill;
            }
        }

        $companyName = null;
        if (method_exists($user, 'getCompany') && $user->getCompany()) {
            $companyName = method_exists($user->getCompany(), 'getName') ? $user->getCompany()->getName() : null;
        }

        // Gestion flexible de created_at
        $createdAt = null;
        if (method_exists($user, 'getCreatedAt') && $user->getCreatedAt()) {
            $createdAt = $user->getCreatedAt()->format('Y-m-d\TH:i:s\Z');
        } elseif (method_exists($user, 'getCreated') && $user->getCreated()) {
            $createdAt = $user->getCreated()->format('Y-m-d\TH:i:s\Z');
        } else {
            $createdAt = (new \DateTime())->format('Y-m-d\TH:i:s\Z');
        }

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'first_name' => method_exists($user, 'getFirstName') ? $user->getFirstName() : null,
            'last_name' => method_exists($user, 'getLastName') ? $user->getLastName() : null,
            'full_name' => trim(
                (method_exists($user, 'getFirstName') ? $user->getFirstName() : '') . ' ' . 
                (method_exists($user, 'getLastName') ? $user->getLastName() : '')
            ),
            'bio' => method_exists($user, 'getBio') ? $user->getBio() : null,
            'location' => method_exists($user, 'getLocation') ? $user->getLocation() : null,
            'profile_picture' => method_exists($user, 'getProfilePicture') ? $user->getProfilePicture() : null,
            'company_name' => $companyName,
            'skills' => $skills,
            'user_type' => method_exists($user, 'getUserType') ? $user->getUserType() : 'user',
            'is_verified' => method_exists($user, 'isVerified') ? $user->isVerified() : false,
            'created_at' => $createdAt,
            'roles' => $user->getRoles()
        ];
    }

    private function buildSearchQuery(array $criteria): array
    {
        $must = [];

        if (!empty($criteria['query'])) {
            $must[] = [
                'multi_match' => [
                    'query' => $criteria['query'],
                    'fields' => ['full_name^3', 'email^2', 'bio', 'skills^2', 'company_name'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        }

        if (!empty($criteria['skills'])) {
            foreach ($criteria['skills'] as $skill) {
                $must[] = ['match' => ['skills' => $skill]];
            }
        }

        if (!empty($criteria['location'])) {
            $must[] = ['match' => ['location' => $criteria['location']]];
        }

        if (!empty($criteria['type'])) {
            $must[] = ['term' => ['user_type' => $criteria['type']]];
        }

        return empty($must) ? ['match_all' => new \stdClass()] : ['bool' => ['must' => $must]];
    }

    private function buildAggregations(): array
    {
        return [
            'top_skills' => ['terms' => ['field' => 'skills.keyword', 'size' => 20]],
            'locations' => ['terms' => ['field' => 'location.keyword', 'size' => 10]],
            'user_types' => ['terms' => ['field' => 'user_type.keyword', 'size' => 5]]
        ];
    }

    private function formatHits(array $hits): array
    {
        return array_map(fn($hit) => array_merge($hit['_source'], ['score' => $hit['_score']]), $hits);
    }

    private function formatAggregations(array $aggs): array
    {
        $formatted = [];
        foreach ($aggs as $name => $data) {
            if (isset($data['buckets'])) {
                $formatted[$name] = array_map(fn($bucket) => ['key' => $bucket['key'], 'count' => $bucket['doc_count']], $data['buckets']);
            }
        }
        return $formatted;
    }
}
