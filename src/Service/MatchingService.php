<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MatchingService
{
    private OpenAIService $openAIService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    
    private const SYSTEM_PROMPT = "Tu es un expert en recrutement pour MIDDO.

Analyse un projet et des utilisateurs pour identifier les meilleurs matchs.

Format JSON :
{
  \"matches\": [
    {
      \"user_id\": 123,
      \"username\": \"nom\",
      \"score\": 92,
      \"raisons\": [\"raison 1\", \"raison 2\"],
      \"competences_cles\": [\"compétence 1\"],
      \"type_profil\": \"freelancer\",
      \"recommandation\": \"Excellent match\"
    }
  ],
  \"total_analyses\": 10,
  \"meilleurs_profils\": 5
}

Score de 0 à 100. Sois objectif.";
    
    public function __construct(
        OpenAIService $openAIService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->openAIService = $openAIService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    
    public function findBestMatches(Project $project, int $limit = 10): array
    {
        try {
            if (!$this->openAIService->isConfigured()) {
                return $this->getSimpleMatches($project, $limit);
            }
            
            $users = $this->entityManager->getRepository(User::class)
                ->createQueryBuilder('u')
                ->where('u.id != :creator_id')
                ->setParameter('creator_id', $project->getCreator()->getId())
                ->setMaxResults(50)
                ->getQuery()
                ->getResult();
            
            if (empty($users)) {
                return [
                    'success' => true,
                    'matches' => [],
                    'message' => 'Aucun utilisateur disponible.',
                ];
            }
            
            $projectContext = $this->buildProjectContext($project);
            $usersContext = $this->buildUsersContext($users);
            
            $userMessage = "PROJET :
{$projectContext}

UTILISATEURS :
{$usersContext}

Identifie les {$limit} meilleurs matchs JSON.";
            
            $messages = [
                $this->openAIService->createSystemMessage(self::SYSTEM_PROMPT),
                $this->openAIService->createUserMessage($userMessage),
            ];
            
            $this->logger->info('Generating matches', [
                'project_id' => $project->getId(),
                'users_count' => count($users),
            ]);
            
            $result = $this->openAIService->chatJson($messages, 'gpt-4o-mini');
            $matches = $this->enrichMatches($result['matches'] ?? [], $users);
            $matches = array_slice($matches, 0, $limit);
            
            return [
                'success' => true,
                'matches' => $matches,
                'total_analyzed' => count($users),
                'error' => null,
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('Error generating matches', [
                'project_id' => $project->getId(),
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'matches' => $this->getSimpleMatches($project, $limit)['matches'],
                'error' => 'Erreur lors du matching.',
            ];
        }
    }
    
    private function buildProjectContext(Project $project): string
    {
        return "Titre: {$project->getTitle()}
Description: {$project->getDescription()}
Budget: {$project->getBudget()} €
Statut: {$project->getStatus()}
Créateur: {$project->getCreator()->getUsername()}";
    }
    
    private function buildUsersContext(array $users): string
    {
        $context = [];
        
        foreach ($users as $user) {
            $skills = $user->getSkills() ? implode(', ', array_map(fn($s) => $s->getName(), $user->getSkills()->toArray())) : 'Non spécifiées';
            
            $context[] = "- ID: {$user->getId()}, Nom: {$user->getUsername()}, Type: {$user->getUserType()}, Compétences: {$skills}";
        }
        
        return implode("\n", $context);
    }
    
    private function enrichMatches(array $matches, array $users): array
    {
        $usersById = [];
        foreach ($users as $user) {
            $usersById[$user->getId()] = $user;
        }
        
        $enrichedMatches = [];
        
        foreach ($matches as $match) {
            $userId = $match['user_id'] ?? null;
            
            if (!$userId || !isset($usersById[$userId])) {
                continue;
            }
            
            $user = $usersById[$userId];
            
            $enrichedMatches[] = [
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'user_type' => $user->getUserType(),
                'email' => $user->getEmail(),
                'bio' => $user->getBio(),
                'score' => $match['score'] ?? 70,
                'raisons' => $match['raisons'] ?? ['Profil correspondant'],
                'competences_cles' => $match['competences_cles'] ?? [],
                'recommandation' => $match['recommandation'] ?? 'Bon candidat',
            ];
        }
        
        usort($enrichedMatches, fn($a, $b) => $b['score'] <=> $a['score']);
        
        return $enrichedMatches;
    }
    
    private function getSimpleMatches(Project $project, int $limit): array
    {
        $users = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.id != :creator_id')
            ->setParameter('creator_id', $project->getCreator()->getId())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        
        $matches = [];
        
        foreach ($users as $user) {
            $matches[] = [
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'user_type' => $user->getUserType(),
                'email' => $user->getEmail(),
                'bio' => $user->getBio(),
                'score' => 70,
                'raisons' => ['Utilisateur actif'],
                'competences_cles' => [],
                'recommandation' => 'Profil intéressant',
            ];
        }
        
        return [
            'success' => true,
            'matches' => $matches,
            'total_analyzed' => count($users),
        ];
    }
}