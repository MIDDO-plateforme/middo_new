<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class ChatbotService
{
    private OpenAIService $openAIService;
    private LoggerInterface $logger;
    
    private const SYSTEM_PROMPT = "Tu es l'assistant virtuel de MIDDO, une plateforme collaborative innovante.

Ta mission : Aider les utilisateurs à naviguer, répondre aux questions, guider dans la création de projets.

Ton style : Moderne, accessible (tutoiement), concis, encourageant. Utilise des emojis modérés.

Réponds toujours en français.";
    
    public function __construct(OpenAIService $openAIService, LoggerInterface $logger)
    {
        $this->openAIService = $openAIService;
        $this->logger = $logger;
    }
    
    public function chat(string $userMessage, array $conversationHistory = [], array $context = []): array
    {
        try {
            if (!$this->openAIService->isConfigured()) {
                return [
                    'success' => false,
                    'message' => '',
                    'error' => 'Service IA non configuré.',
                ];
            }
            
            $systemPrompt = self::SYSTEM_PROMPT;
            
            if (!empty($context)) {
                $systemPrompt .= "\n\nContexte actuel:\n";
                
                if (isset($context['user'])) {
                    $systemPrompt .= "- Utilisateur: {$context['user']['username']}\n";
                }
                
                if (isset($context['project'])) {
                    $systemPrompt .= "- Projet: {$context['project']['title']}\n";
                }
            }
            
            $messages = [$this->openAIService->createSystemMessage($systemPrompt)];
            
            $recentHistory = array_slice($conversationHistory, -10);
            foreach ($recentHistory as $msg) {
                $messages[] = [
                    'role' => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? $msg['message'] ?? '',
                ];
            }
            
            $messages[] = $this->openAIService->createUserMessage($userMessage);
            
            $this->logger->info('Chatbot request', ['user_message' => $userMessage]);
            
            $response = $this->openAIService->chat($messages, null, [
                'temperature' => 0.8,
                'max_tokens' => 500,
            ]);
            
            $botMessage = $this->openAIService->extractContent($response);
            
            $this->logger->info('Chatbot response generated');
            
            return [
                'success' => true,
                'message' => $botMessage,
                'error' => null,
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('Chatbot error', ['message' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => '',
                'error' => 'Une erreur est survenue.',
            ];
        }
    }
    
    public function getQuickAnswer(string $question): ?array
    {
        $question = strtolower(trim($question));
        
        $quickAnswers = [
            'comment créer un projet' => [
                'message' => "Pour créer un projet sur MIDDO :\n\n1. Va dans 'Mes Projets'\n2. Clique 'Créer un projet'\n3. Remplis les infos\n4. Valide !\n\nTon projet sera visible pour collaboration. 🚀",
                'success' => true,
            ],
            'comment trouver des collaborateurs' => [
                'message' => "Pour trouver des collaborateurs :\n\n1. Utilise le Matching sur ton projet\n2. Consulte la liste utilisateurs\n3. Envoie des messages\n4. Notre IA te suggère les meilleurs matchs ! 💪",
                'success' => true,
            ],
            'comment ça marche' => [
                'message' => "MIDDO c'est ta plateforme de collaboration ! 🌟\n\n✅ Crée des projets\n✅ Trouve des collaborateurs\n✅ Communique facilement\n✅ IA intégrée\n\nQue veux-tu faire ?",
                'success' => true,
            ],
        ];
        
        foreach ($quickAnswers as $key => $answer) {
            if (stripos($question, $key) !== false) {
                return $answer;
            }
        }
        
        return null;
    }
}