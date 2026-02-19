<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class FallbackManager
{
    private LoggerInterface $logger;
    private array $fallbackStats = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getSuggestions(): array
    {
        $this->logger->info('🔄 [FALLBACK] Mode DEMO activé pour Suggestions');
        $this->fallbackStats['suggestions'] = ($this->fallbackStats['suggestions'] ?? 0) + 1;

        return [
            "🎯 Définir clairement les objectifs SMART du projet",
            "💰 Établir un budget réaliste avec marge de sécurité 15%",
            "👥 Identifier les parties prenantes clés et leur rôle",
            "📅 Créer un planning détaillé avec jalons et deadlines",
            "🧑‍💼 Prévoir des ressources humaines qualifiées et disponibles"
        ];
    }

    public function getMatches(): array
    {
        $this->logger->info('🔄 [FALLBACK] Mode DEMO activé pour Matching');
        $this->fallbackStats['matching'] = ($this->fallbackStats['matching'] ?? 0) + 1;

        return [
            [
                'title' => '👨‍💼 Chef de Projet Digital',
                'skills' => ['Agile', 'Scrum', 'Leadership'],
                'value' => 'chef.projet@example.com',
                'score' => 95
            ],
            [
                'title' => '💻 Développeur Full Stack Senior',
                'skills' => ['PHP', 'Symfony', 'React'],
                'value' => 'dev.fullstack@example.com',
                'score' => 92
            ],
            [
                'title' => '🎨 Designer UX/UI Expert',
                'skills' => ['Figma', 'Prototypage', 'Design System'],
                'value' => 'designer.ux@example.com',
                'score' => 89
            ],
            [
                'title' => '📊 Data Analyst',
                'skills' => ['Python', 'SQL', 'Tableau'],
                'value' => 'data.analyst@example.com',
                'score' => 87
            ],
            [
                'title' => '🔒 Expert Cybersécurité',
                'skills' => ['Pentesting', 'OWASP', 'ISO 27001'],
                'value' => 'cyber.expert@example.com',
                'score' => 85
            ]
        ];
    }

    public function getSentiment(): array
    {
        $this->logger->info('🔄 [FALLBACK] Mode DEMO activé pour Sentiment');
        $this->fallbackStats['sentiment'] = ($this->fallbackStats['sentiment'] ?? 0) + 1;

        return [
            'sentiment' => 'positif',
            'emotion' => 'optimiste',
            'confidence' => 0.85
        ];
    }

    public function getStats(): array
    {
        return $this->fallbackStats;
    }
}