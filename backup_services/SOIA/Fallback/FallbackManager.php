<?php

namespace App\Service\SOIA\Fallback;

use Psr\Log\LoggerInterface;

class FallbackManager
{
    private LoggerInterface $logger;
    private array $fallbackStats = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(callable $primary, string $apiName): array
    {
        try {
            return $primary();
        } catch (\Exception $e) {
            $this->logger->warning("Fallback activé pour [{$apiName}]", [
                'error' => $e->getMessage()
            ]);
            
            $this->recordFallback($apiName);
            
            return $this->getFallbackResponse($apiName);
        }
    }

    private function recordFallback(string $apiName): void
    {
        if (!isset($this->fallbackStats[$apiName])) {
            $this->fallbackStats[$apiName] = 0;
        }
        $this->fallbackStats[$apiName]++;
    }

    private function getFallbackResponse(string $apiName): array
    {
        $responses = [
            'suggestions' => [
                'success' => true,
                'suggestions' => [
                    '🎯 Définir clairement les objectifs SMART du projet',
                    '💰 Établir un budget réaliste avec marge de sécurité 15%',
                    '👥 Identifier les parties prenantes clés et leurs attentes',
                    '📅 Créer un planning détaillé avec jalons critiques',
                    '🧑‍💼 Prévoir des ressources humaines qualifiées et disponibles'
                ],
                'count' => 5,
                'demo_mode' => true,
                'soia_fallback' => true
            ],
            'matching' => [
                'success' => true,
                'matches' => [
                    ['title' => '👨‍💼 Chef de Projet Digital', 'skills' => ['Agile', 'Scrum'], 'score' => 95],
                    ['title' => '💻 Développeur Full Stack', 'skills' => ['PHP', 'Symfony'], 'score' => 92],
                    ['title' => '🎨 Designer UX/UI', 'skills' => ['Figma', 'Prototypage'], 'score' => 88],
                    ['title' => '📊 Expert Marketing', 'skills' => ['SEO', 'Analytics'], 'score' => 85],
                    ['title' => '📈 Data Analyst', 'skills' => ['SQL', 'Power BI'], 'score' => 82]
                ],
                'total' => 5,
                'demo_mode' => true,
                'soia_fallback' => true
            ],
            'sentiment' => [
                'success' => true,
                'sentiment' => 'positif',
                'emotion' => 'optimiste',
                'confidence' => 0.85,
                'demo_mode' => true,
                'soia_fallback' => true
            ]
        ];

        return $responses[$apiName] ?? ['error' => 'Unknown API'];
    }

    public function getTotalFallbacks(): int
    {
        return array_sum($this->fallbackStats);
    }

    public function getStats(): array
    {
        return $this->fallbackStats;
    }
}