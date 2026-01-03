<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class ChatbotService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private string $openaiApiKey;
    private string $anthropicApiKey;

    // Contexte conversationnel en mémoire (pour une session)
    private array $conversationHistory = [];

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        string $openaiApiKey,
        string $anthropicApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->openaiApiKey = $openaiApiKey;
        $this->anthropicApiKey = $anthropicApiKey;
    }

    /**
     * 💬 Traite un message utilisateur et retourne une réponse intelligente
     */
    public function chat(string $userMessage, ?array $userContext = null, ?string $sessionId = null): array
    {
        try {
            // Ajouter le message utilisateur à l'historique
            $this->addToHistory('user', $userMessage, $sessionId);

            // Construire le contexte enrichi
            $systemPrompt = $this->buildSystemPrompt($userContext);
            $messages = $this->buildMessageHistory($systemPrompt);

            // Appeler l'IA
            $response = $this->callOpenAI($messages);
            
            // Ajouter la réponse à l'historique
            $this->addToHistory('assistant', $response['content'], $sessionId);

            return [
                'success' => true,
                'response' => $response['content'],
                'intent' => $response['intent'] ?? 'general',
                'suggested_actions' => $response['suggested_actions'] ?? [],
                'provider' => 'openai',
                'timestamp' => new \DateTime()
            ];

        } catch (\Exception $e) {
            $this->logger->warning('OpenAI chatbot failed, trying Anthropic', [
                'error' => $e->getMessage()
            ]);

            try {
                $systemPrompt = $this->buildSystemPrompt($userContext);
                $response = $this->callAnthropic($userMessage, $systemPrompt);
                
                $this->addToHistory('assistant', $response['content'], $sessionId);

                return [
                    'success' => true,
                    'response' => $response['content'],
                    'intent' => $response['intent'] ?? 'general',
                    'suggested_actions' => $response['suggested_actions'] ?? [],
                    'provider' => 'anthropic',
                    'timestamp' => new \DateTime()
                ];

            } catch (\Exception $e2) {
                $this->logger->error('All AI providers failed for chatbot', [
                    'openai_error' => $e->getMessage(),
                    'anthropic_error' => $e2->getMessage()
                ]);

                return [
                    'success' => false,
                    'response' => $this->getFallbackResponse($userMessage),
                    'intent' => 'unknown',
                    'suggested_actions' => $this->getFallbackActions(),
                    'provider' => 'fallback',
                    'timestamp' => new \DateTime()
                ];
            }
        }
    }

    /**
     * 🧠 Analyse l'intention de l'utilisateur
     */
    public function analyzeIntent(string $userMessage): array
    {
        try {
            $prompt = "Analyse l'intention de ce message utilisateur et catégorise-le.\n\n";
            $prompt .= "Message : \"{$userMessage}\"\n\n";
            $prompt .= "Catégories possibles : navigation, question, création, modification, suppression, aide, autre\n";
            $prompt .= "Format de réponse (JSON uniquement) :\n";
            $prompt .= '{
                "intent": "navigation|question|création|modification|suppression|aide|autre",
                "confidence": 0.95,
                "entities": {
                    "action": "créer",
                    "target": "workspace",
                    "details": "projet marketing"
                },
                "suggested_route": "/workspace/new",
                "parameters": {}
            }';

            $response = $this->callOpenAI([
                ['role' => 'system', 'content' => 'Tu es un analyseur d\'intentions expert. Réponds UNIQUEMENT en JSON valide.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            return [
                'success' => true,
                'intent' => $response['intent'] ?? 'autre',
                'confidence' => $response['confidence'] ?? 0.5,
                'entities' => $response['entities'] ?? [],
                'suggested_route' => $response['suggested_route'] ?? null,
                'parameters' => $response['parameters'] ?? [],
                'provider' => 'openai'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'intent' => 'autre',
                'confidence' => 0,
                'entities' => [],
                'suggested_route' => null,
                'parameters' => [],
                'provider' => 'fallback'
            ];
        }
    }

    /**
     * 📚 Génère des suggestions contextuelles
     */
    public function generateContextualSuggestions(?array $userContext = null): array
    {
        try {
            $contextStr = json_encode($userContext ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            $prompt = "Basé sur le contexte utilisateur suivant, génère 5 suggestions pertinentes de questions ou d'actions.\n\n";
            $prompt .= "Contexte :\n{$contextStr}\n\n";
            $prompt .= "Format de réponse (JSON uniquement) :\n";
            $prompt .= '{
                "suggestions": [
                    {
                        "text": "Créer un nouveau workspace",
                        "action": "navigate",
                        "target": "/workspace/new",
                        "icon": "➕"
                    }
                ]
            }';

            $response = $this->callOpenAI([
                ['role' => 'system', 'content' => 'Tu es un assistant proactif. Réponds UNIQUEMENT en JSON valide.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            return [
                'success' => true,
                'suggestions' => $response['suggestions'] ?? [],
                'provider' => 'openai'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'suggestions' => $this->getFallbackSuggestions($userContext),
                'provider' => 'fallback'
            ];
        }
    }

    /**
     * 🔍 Recherche intelligente dans la plateforme
     */
    public function intelligentSearch(string $query, ?array $userContext = null): array
    {
        try {
            $contextStr = json_encode($userContext ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            $prompt = "L'utilisateur recherche : \"{$query}\"\n\n";
            $prompt .= "Contexte utilisateur :\n{$contextStr}\n\n";
            $prompt .= "Identifie ce que l'utilisateur recherche et suggère les résultats les plus pertinents.\n";
            $prompt .= "Format de réponse (JSON uniquement) :\n";
            $prompt .= '{
                "search_type": "workspace|document|project|task|user",
                "filters": {
                    "status": "active",
                    "visibility": "public"
                },
                "suggested_results": [
                    {
                        "type": "workspace",
                        "id": 123,
                        "title": "Projet Marketing",
                        "relevance_score": 0.95,
                        "why": "Correspond à votre recherche"
                    }
                ],
                "alternative_searches": ["Autre suggestion de recherche"]
            }';

            $response = $this->callOpenAI([
                ['role' => 'system', 'content' => 'Tu es un moteur de recherche intelligent. Réponds UNIQUEMENT en JSON valide.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            return [
                'success' => true,
                'search_type' => $response['search_type'] ?? 'general',
                'filters' => $response['filters'] ?? [],
                'suggested_results' => $response['suggested_results'] ?? [],
                'alternative_searches' => $response['alternative_searches'] ?? [],
                'provider' => 'openai'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'search_type' => 'general',
                'filters' => [],
                'suggested_results' => [],
                'alternative_searches' => [],
                'provider' => 'fallback'
            ];
        }
    }

    /**
     * 💾 Sauvegarde l'historique de conversation
     */
    public function saveConversationHistory(string $sessionId): bool
    {
        try {
            // TODO: Implémenter la sauvegarde en base de données
            // Pour l'instant, on garde l'historique en mémoire
            
            $this->logger->info('Conversation history saved', [
                'session_id' => $sessionId,
                'message_count' => count($this->conversationHistory)
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to save conversation history', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 🔄 Charge l'historique de conversation
     */
    public function loadConversationHistory(string $sessionId): array
    {
        try {
            // TODO: Implémenter le chargement depuis la base de données
            
            return [
                'success' => true,
                'messages' => $this->conversationHistory,
                'session_id' => $sessionId
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'messages' => [],
                'session_id' => $sessionId
            ];
        }
    }

    /**
     * 🧹 Réinitialise la conversation
     */
    public function resetConversation(?string $sessionId = null): void
    {
        $this->conversationHistory = [];
        
        $this->logger->info('Conversation reset', [
            'session_id' => $sessionId
        ]);
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * 🔧 PRIVATE: Construit le prompt système
     */
    private function buildSystemPrompt(?array $userContext): string
    {
        $systemPrompt = "Tu es MIDDO Assistant, l'assistant IA intelligent de la plateforme MIDDO.\n\n";
        $systemPrompt .= "MIDDO est une plateforme collaborative innovante permettant de :\n";
        $systemPrompt .= "- Créer et gérer des Workspaces (espaces de travail collaboratifs)\n";
        $systemPrompt .= "- Organiser des Documents (texte, fichiers, liens)\n";
        $systemPrompt .= "- Piloter des Projets avec dashboards personnalisés\n";
        $systemPrompt .= "- Suivre des Tâches avec checklists et time tracking\n";
        $systemPrompt .= "- Gérer des Permissions granulaires (OWNER, ADMIN, MEMBER, VIEWER)\n";
        $systemPrompt .= "- Collaborer via Commentaires et Activités\n\n";
        
        $systemPrompt .= "Tu dois :\n";
        $systemPrompt .= "1. Répondre en français de manière amicale et encourageante (style Génération Z)\n";
        $systemPrompt .= "2. Être avant-gardiste et innovant dans tes suggestions\n";
        $systemPrompt .= "3. Aider l'utilisateur à naviguer et utiliser toutes les fonctionnalités\n";
        $systemPrompt .= "4. Proposer des actions concrètes et des raccourcis\n";
        $systemPrompt .= "5. Comprendre le contexte et personnaliser tes réponses\n\n";

        if ($userContext) {
            $systemPrompt .= "CONTEXTE UTILISATEUR ACTUEL :\n";
            $systemPrompt .= json_encode($userContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }

        $systemPrompt .= "Réponds de manière naturelle et conversationnelle, comme un vrai assistant humain.";

        return $systemPrompt;
    }

    /**
     * 🔧 PRIVATE: Construit l'historique des messages
     */
    private function buildMessageHistory(string $systemPrompt): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Limite à 10 derniers messages pour éviter de dépasser les tokens
        $recentHistory = array_slice($this->conversationHistory, -10);

        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }

        return $messages;
    }

    /**
     * 🔧 PRIVATE: Ajoute un message à l'historique
     */
    private function addToHistory(string $role, string $content, ?string $sessionId): void
    {
        $this->conversationHistory[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => new \DateTime(),
            'session_id' => $sessionId
        ];
    }

    /**
     * 🤖 PRIVATE: Appel OpenAI avec messages
     */
    private function callOpenAI(array $messages): array
    {
        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->openaiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => 0.8,
                'max_tokens' => 1000
            ]
        ]);

        $data = $response->toArray();
        $content = $data['choices'][0]['message']['content'] ?? 'Désolé, je n\'ai pas pu générer une réponse.';

        // Essayer de parser en JSON si possible (pour les méthodes qui retournent du JSON)
        $jsonContent = preg_replace('/```json\s*|\s*```/', '', $content);
        $jsonContent = trim($jsonContent);
        $parsedJson = json_decode($jsonContent, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($parsedJson)) {
            return $parsedJson;
        }

        // Sinon, retourner le contenu brut
        return [
            'content' => $content,
            'intent' => 'general',
            'suggested_actions' => []
        ];
    }

    /**
     * 🤖 PRIVATE: Appel Anthropic
     */
    private function callAnthropic(string $userMessage, string $systemPrompt): array
    {
        $response = $this->httpClient->request('POST', 'https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $this->anthropicApiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1000,
                'system' => $systemPrompt,
                'messages' => array_merge(
                    array_map(fn($msg) => [
                        'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                        'content' => $msg['content']
                    ], array_slice($this->conversationHistory, -10)),
                    [['role' => 'user', 'content' => $userMessage]]
                )
            ]
        ]);

        $data = $response->toArray();
        $content = $data['content'][0]['text'] ?? 'Désolé, je n\'ai pas pu générer une réponse.';

        return [
            'content' => $content,
            'intent' => 'general',
            'suggested_actions' => []
        ];
    }

    /**
     * 🔄 PRIVATE: Réponse de secours
     */
    private function getFallbackResponse(string $userMessage): string
    {
        $lowerMessage = strtolower($userMessage);

        if (strpos($lowerMessage, 'bonjour') !== false || strpos($lowerMessage, 'salut') !== false) {
            return "Hey ! 👋 Bienvenue sur MIDDO ! Je suis là pour t'aider. Que veux-tu faire aujourd'hui ?";
        }

        if (strpos($lowerMessage, 'workspace') !== false || strpos($lowerMessage, 'espace') !== false) {
            return "Les Workspaces sont des espaces collaboratifs où tu peux organiser tes projets ! Veux-tu en créer un nouveau ? 🚀";
        }

        if (strpos($lowerMessage, 'projet') !== false) {
            return "Super ! Les projets dans MIDDO te permettent de structurer ton travail avec des dashboards personnalisés. Comment puis-je t'aider ? 📊";
        }

        if (strpos($lowerMessage, 'tâche') !== false || strpos($lowerMessage, 'task') !== false) {
            return "Les tâches dans MIDDO incluent des checklists, du time tracking et des assignations intelligentes ! Besoin d'aide pour en créer une ? ✅";
        }

        if (strpos($lowerMessage, 'aide') !== false || strpos($lowerMessage, 'help') !== false) {
            return "Pas de souci ! Je peux t'aider avec :\n- Créer des workspaces, projets, tâches\n- Gérer les permissions\n- Organiser tes documents\n- Et bien plus ! Qu'est-ce qui t'intéresse ? 💡";
        }

        return "Je suis là pour t'aider avec MIDDO ! Pose-moi une question sur les workspaces, projets, tâches, ou dis-moi ce que tu veux faire. 😊";
    }

    /**
     * 🔄 PRIVATE: Actions de secours
     */
    private function getFallbackActions(): array
    {
        return [
            ['text' => '➕ Créer un workspace', 'action' => 'navigate', 'target' => '/workspace/new'],
            ['text' => '📊 Voir mes projets', 'action' => 'navigate', 'target' => '/workspace'],
            ['text' => '❓ Aide', 'action' => 'help', 'target' => null]
        ];
    }

    /**
     * 🔄 PRIVATE: Suggestions de secours
     */
    private function getFallbackSuggestions(?array $userContext): array
    {
        $suggestions = [
            ['text' => 'Créer un nouveau workspace', 'action' => 'navigate', 'target' => '/workspace/new', 'icon' => '➕'],
            ['text' => 'Voir mes workspaces', 'action' => 'navigate', 'target' => '/workspace', 'icon' => '📁'],
            ['text' => 'Explorer les fonctionnalités', 'action' => 'help', 'target' => null, 'icon' => '🔍']
        ];

        if ($userContext && isset($userContext['current_workspace'])) {
            $suggestions[] = [
                'text' => 'Ajouter un document',
                'action' => 'navigate',
                'target' => '/workspace/' . $userContext['current_workspace'] . '/document/new',
                'icon' => '📄'
            ];
        }

        return $suggestions;
    }
}
