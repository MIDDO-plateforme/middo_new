<?php

namespace App\Service;

use App\Entity\Project;
use Psr\Log\LoggerInterface;

class SuggestionService
{
    private OpenAIService $openAIService;
    private LoggerInterface $logger;
    
    private const SYSTEM_PROMPT = "Tu es un expert en gestion de projet sur MIDDO.

Analyse les projets et propose des améliorations concrètes.

Format JSON :
{
  \"score_global\": 75,
  \"points_forts\": [\"point 1\", \"point 2\"],
  \"points_amelioration\": [\"amélioration 1\", \"amélioration 2\"],
  \"suggestions\": [
    {
      \"categorie\": \"Description\",
      \"priorite\": \"haute\",
      \"suggestion\": \"Détail\",
      \"impact\": \"Impact attendu\"
    }
  ],
  \"competences_recherchees\": [\"compétence 1\"],
  \"estimation_budget\": \"Recommandation\",
  \"prochaines_etapes\": [\"étape 1\", \"étape 2\"]
}

Sois constructif et précis.";
    
    public function __construct(OpenAIService $openAIService, LoggerInterface $logger)
    {
        $this->openAIService = $openAIService;
        $this->logger = $logger;
    }
    
    public function suggestImprovements(Project $project): array
    {
        try {
            if (!$this->openAIService->isConfigured()) {
                return $this->getDefaultSuggestions();
            }
            
            $projectContext = $this->buildProjectContext($project);
            
            $userMessage = "Analyse ce projet :

Titre : {$project->getTitle()}
Description : {$project->getDescription()}
Budget : {$project->getBudget()} €
Statut : {$project->getStatus()}

Contexte : {$projectContext}

Fournis une analyse complète JSON.";
            
            $messages = [
                $this->openAIService->createSystemMessage(self::SYSTEM_PROMPT),
                $this->openAIService->createUserMessage($userMessage),
            ];
            
            $this->logger->info('Generating suggestions', ['project_id' => $project->getId()]);
            
            $suggestions = $this->openAIService->chatJson($messages);
            $suggestions = $this->validateAndEnrichSuggestions($suggestions, $project);
            
            return [
                'success' => true,
                'data' => $suggestions,
                'error' => null,
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('Error generating suggestions', [
                'project_id' => $project->getId(),
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'data' => $this->getDefaultSuggestions(),
                'error' => 'Erreur lors de l\'analyse.',
            ];
        }
    }
    
    private function buildProjectContext(Project $project): string
    {
        $context = [];
        
        $createdAt = $project->getCreatedAt();
        if ($createdAt) {
            $days = $createdAt->diff(new \DateTime())->days;
            $context[] = "Créé il y a {$days} jours";
        }
        
        $creatorType = $project->getOwner()->getUserType();
        $context[] = "Créé par un(e) {$creatorType}";
        
        return implode(", ", $context);
    }
    
    private function validateAndEnrichSuggestions(array $suggestions, Project $project): array
    {
        $suggestions['score_global'] = $suggestions['score_global'] ?? 70;
        $suggestions['points_forts'] = $suggestions['points_forts'] ?? [];
        $suggestions['points_amelioration'] = $suggestions['points_amelioration'] ?? [];
        $suggestions['suggestions'] = $suggestions['suggestions'] ?? [];
        $suggestions['competences_recherchees'] = $suggestions['competences_recherchees'] ?? [];
        $suggestions['prochaines_etapes'] = $suggestions['prochaines_etapes'] ?? [];
        
        $suggestions['project_id'] = $project->getId();
        $suggestions['analyzed_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        
        return $suggestions;
    }
    
    private function getDefaultSuggestions(): array
    {
        return [
            'score_global' => 70,
            'points_forts' => ['Projet bien structuré', 'Objectifs clairs'],
            'points_amelioration' => ['Détailler la description', 'Préciser les compétences'],
            'suggestions' => [
                [
                    'categorie' => 'Description',
                    'priorite' => 'moyenne',
                    'suggestion' => 'Enrichir avec plus de détails',
                    'impact' => 'Attirer plus de collaborateurs',
                ],
            ],
            'competences_recherchees' => ['Développement', 'Design'],
            'prochaines_etapes' => ['Finaliser description', 'Publier projet', 'Contacter collaborateurs'],
        ];
    }
}