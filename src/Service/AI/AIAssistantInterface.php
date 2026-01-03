<?php

namespace App\Service\AI;

/**
 * Interface pour les futurs services d'IA (ChatGPT, Claude, Gemini, etc.)
 * 
 * Cette interface sera implémentée en SESSION 13+ quand on intégrera l'IA.
 * Pour l'instant, elle sert de documentation et de structure.
 * 
 * Cas d'usage prévus:
 * - Assistant création de projets (suggestions descriptions, budgets)
 * - Matching intelligent users <-> projets
 * - Chatbot support utilisateurs
 * - Recommandations personnalisées
 */
interface AIAssistantInterface
{
    /**
     * Génère une réponse IA basée sur un prompt utilisateur
     * 
     * @param string $prompt Le message/question de l'utilisateur
     * @param array $context Contexte additionnel (user, project, etc.)
     * @return string La réponse générée par l'IA
     */
    public function generateResponse(string $prompt, array $context = []): string;
    
    /**
     * Suggère des améliorations pour une description de projet
     * 
     * @param string $description Description actuelle du projet
     * @return array Suggestions d'amélioration
     */
    public function suggestProjectImprovements(string $description): array;
    
    /**
     * Trouve des utilisateurs compatibles avec un projet
     * 
     * @param int $projectId ID du projet
     * @return array Liste d'utilisateurs recommandés
     */
    public function matchUsersToProject(int $projectId): array;
    
    /**
     * Analyse le sentiment d'un texte (positif/négatif/neutre)
     * 
     * @param string $text Texte à analyser
     * @return string Sentiment détecté
     */
    public function analyzeSentiment(string $text): string;
}