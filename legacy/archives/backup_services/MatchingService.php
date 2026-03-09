<?php

namespace App\Service;

class MatchingService
{
    private $missions = [];
    private $userProfile = [];

    public function __construct()
    {
        $this->initializeMissions();
    }

    private function initializeMissions()
    {
        // Missions simulées (en production: depuis BDD)
        $this->missions = [
            [
                'id' => 1,
                'title' => 'Développement Smart Contract DeFi',
                'description' => 'Création d\'un smart contract pour plateforme DeFi',
                'budget' => 3500,
                'duration' => '2 mois',
                'company' => 'DeFi Corp',
                'skills' => ['Solidity', 'Ethereum', 'Smart Contracts', 'Web3'],
                'location' => 'Remote',
                'urgency' => 'high',
            ],
            [
                'id' => 2,
                'title' => 'Audit Sécurité Application Symfony',
                'description' => 'Audit de sécurité complet d\'une application Symfony 6.3',
                'budget' => 1200,
                'duration' => '2 semaines',
                'company' => 'TechStart SAS',
                'skills' => ['Symfony', 'PHP', 'Sécurité', 'OWASP'],
                'location' => 'Paris, France',
                'urgency' => 'medium',
            ],
            [
                'id' => 3,
                'title' => 'Développement dApp Polygon',
                'description' => 'Création d\'une dApp complète sur Polygon avec React frontend',
                'budget' => 4500,
                'duration' => '3 mois',
                'company' => 'BlockChain Ventures',
                'skills' => ['React', 'Web3.js', 'Polygon', 'Smart Contracts'],
                'location' => 'Remote',
                'urgency' => 'high',
            ],
            [
                'id' => 4,
                'title' => 'Conseil Architecture Microservices',
                'description' => 'Conseil et mise en place architecture microservices',
                'budget' => 800,
                'duration' => '1 mois',
                'company' => 'CryptoVentures',
                'skills' => ['Architecture', 'Microservices', 'Docker', 'Kubernetes'],
                'location' => 'Remote',
                'urgency' => 'low',
            ],
            [
                'id' => 5,
                'title' => 'Intégration API Payment Blockchain',
                'description' => 'Intégration d\'API de paiement crypto dans plateforme e-commerce',
                'budget' => 2200,
                'duration' => '6 semaines',
                'company' => 'E-Commerce Plus',
                'skills' => ['PHP', 'API REST', 'Blockchain', 'Crypto'],
                'location' => 'Lyon, France',
                'urgency' => 'medium',
            ],
        ];
    }

    public function findMatches(array $userProfile): array
    {
        $this->userProfile = $userProfile;
        $userSkills = $userProfile['skills'] ?? ['Symfony', 'React', 'Blockchain', 'PHP'];
        $userBudgetMin = $userProfile['budgetMin'] ?? 1000;
        $userLocation = $userProfile['location'] ?? 'Remote';

        $matches = [];

        foreach ($this->missions as $mission) {
            $score = $this->calculateMatchScore($mission, $userSkills, $userBudgetMin, $userLocation);
            
            $matches[] = [
                'mission' => $mission,
                'score' => $score,
                'reasons' => $this->getMatchReasons($mission, $score),
            ];
        }

        // Trier par score décroissant
        usort($matches, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // Retourner top 5
        return array_slice($matches, 0, 5);
    }

    private function calculateMatchScore(array $mission, array $userSkills, int $userBudgetMin, string $userLocation): int
    {
        $score = 0;

        // 1. Compétences (60% du score)
        $skillsMatch = count(array_intersect($mission['skills'], $userSkills));
        $skillsTotal = count($mission['skills']);
        $skillsScore = $skillsTotal > 0 ? ($skillsMatch / $skillsTotal) * 60 : 0;
        $score += $skillsScore;

        // 2. Budget (20% du score)
        if ($mission['budget'] >= $userBudgetMin) {
            $budgetScore = 20;
        } else {
            $budgetScore = ($mission['budget'] / $userBudgetMin) * 20;
        }
        $score += $budgetScore;

        // 3. Localisation (10% du score)
        if ($mission['location'] === $userLocation || $mission['location'] === 'Remote' || $userLocation === 'Remote') {
            $score += 10;
        }

        // 4. Urgence (10% du score)
        if ($mission['urgency'] === 'high') {
            $score += 10;
        } elseif ($mission['urgency'] === 'medium') {
            $score += 5;
        }

        return min(100, round($score));
    }

    private function getMatchReasons(array $mission, int $score): array
    {
        $reasons = [];

        if ($score >= 90) {
            $reasons[] = ' Correspondance exceptionnelle';
        } elseif ($score >= 75) {
            $reasons[] = ' Excellente correspondance';
        } elseif ($score >= 60) {
            $reasons[] = ' Bonne correspondance';
        }

        // Compétences matchées
        $userSkills = $this->userProfile['skills'] ?? ['Symfony', 'React', 'Blockchain', 'PHP'];
        $matchedSkills = array_intersect($mission['skills'], $userSkills);
        if (count($matchedSkills) > 0) {
            $reasons[] = ' ' . count($matchedSkills) . '/' . count($mission['skills']) . ' compétences correspondent';
        }

        // Budget
        if ($mission['budget'] >= 3000) {
            $reasons[] = ' Budget élevé';
        }

        // Remote
        if ($mission['location'] === 'Remote') {
            $reasons[] = ' 100% Remote';
        }

        // Urgence
        if ($mission['urgency'] === 'high') {
            $reasons[] = ' Mission urgente';
        }

        return $reasons;
    }

    public function getMissionById(int $id): ?array
    {
        foreach ($this->missions as $mission) {
            if ($mission['id'] === $id) {
                return $mission;
            }
        }
        return null;
    }
}