<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class SentimentAnalysisService
{
    private OpenAIService $openAIService;
    private LoggerInterface $logger;
    
    private const SYSTEM_PROMPT = "Tu es un expert en analyse de sentiment pour MIDDO.

Analyse le sentiment, le ton et les émotions d'un texte.

Format JSON :
{
  \"sentiment\": \"positif\",
  \"score\": 85,
  \"emotions\": [\"enthousiasme\", \"optimisme\"],
  \"ton\": \"professionnel et motivé\",
  \"confiance\": \"élevé\",
  \"urgence\": false,
  \"indicateurs\": {
    \"mots_positifs\": [\"excellent\", \"réussite\"],
    \"mots_negatifs\": [],
    \"mots_cles\": [\"collaboration\", \"projet\"]
  },
  \"resume\": \"Message très positif\"
}

Score 0-100 : 0=très négatif, 50=neutre, 100=très positif.";
    
    public function __construct(OpenAIService $openAIService, LoggerInterface $logger)
    {
        $this->openAIService = $openAIService;
        $this->logger = $logger;
    }
    
    public function analyze(string $text, array $context = []): array
    {
        try {
            if (!$this->openAIService->isConfigured()) {
                return $this->getSimpleAnalysis($text);
            }
            
            if (empty(trim($text))) {
                return [
                    'success' => false,
                    'error' => 'Texte vide.',
                    'data' => null,
                ];
            }
            
            $contextInfo = '';
            if (!empty($context)) {
                $contextInfo = "\n\nContexte : " . json_encode($context, JSON_UNESCAPED_UNICODE);
            }
            
            $userMessage = "Analyse le sentiment :

TEXTE :
{$text}
{$contextInfo}

Fournis analyse JSON complète.";
            
            $messages = [
                $this->openAIService->createSystemMessage(self::SYSTEM_PROMPT),
                $this->openAIService->createUserMessage($userMessage),
            ];
            
            $this->logger->info('Analyzing sentiment', ['text_length' => strlen($text)]);
            
            $analysis = $this->openAIService->chatJson($messages, 'gpt-4o-mini');
            $analysis = $this->validateAnalysis($analysis);
            
            return [
                'success' => true,
                'data' => $analysis,
                'error' => null,
            ];
            
        } catch (\Exception $e) {
            $this->logger->error('Error analyzing sentiment', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'data' => $this->getSimpleAnalysis($text),
                'error' => 'Erreur lors de l\'analyse.',
            ];
        }
    }
    
    /**
     * Alias pour analyze() - pour compatibilité avec le contrôleur
     */
    public function analyzeSentiment(string $text, array $context = []): array
    {
        $result = $this->analyze($text, $context);
        return $result['data'] ?? $this->getSimpleAnalysis($text);
    }
    
    private function getSimpleAnalysis(string $text): array
    {
        $text = strtolower($text);
        
        $positiveWords = ['bien', 'excellent', 'super', 'génial', 'top', 'parfait', 'réussi', 'succès', 'bravo', 'merci'];
        $negativeWords = ['mal', 'problème', 'erreur', 'bug', 'mauvais', 'nul', 'échec', 'difficile', 'impossible'];
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($text, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($text, $word);
        }
        
        $score = 50;
        if ($positiveCount > $negativeCount) {
            $score = min(100, 50 + ($positiveCount * 10));
            $sentiment = 'positif';
        } elseif ($negativeCount > $positiveCount) {
            $score = max(0, 50 - ($negativeCount * 10));
            $sentiment = 'négatif';
        } else {
            $sentiment = 'neutre';
        }
        
        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'emotions' => $sentiment === 'positif' ? ['optimisme'] : ($sentiment === 'négatif' ? ['inquiétude'] : ['neutralité']),
            'ton' => 'professionnel',
            'confiance' => 'moyen',
            'urgence' => false,
            'indicateurs' => [
                'mots_positifs' => $positiveWords,
                'mots_negatifs' => $negativeWords,
                'mots_cles' => [],
            ],
            'resume' => "Analyse simplifiée : sentiment {$sentiment}",
            'method' => 'simple',
        ];
    }
    
    private function validateAnalysis(array $analysis): array
    {
        $analysis['sentiment'] = $analysis['sentiment'] ?? 'neutre';
        $analysis['score'] = $analysis['score'] ?? 50;
        $analysis['emotions'] = $analysis['emotions'] ?? [];
        $analysis['ton'] = $analysis['ton'] ?? 'professionnel';
        $analysis['confiance'] = $analysis['confiance'] ?? 'moyen';
        $analysis['urgence'] = $analysis['urgence'] ?? false;
        $analysis['indicateurs'] = $analysis['indicateurs'] ?? [];
        $analysis['resume'] = $analysis['resume'] ?? '';
        
        $analysis['score'] = max(0, min(100, $analysis['score']));
        
        $analysis['analyzed_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        $analysis['method'] = 'ai';
        
        return $analysis;
    }
    
    public function analyzeBatch(array $texts): array
    {
        $results = [];
        
        foreach ($texts as $key => $text) {
            $results[$key] = $this->analyze($text);
        }
        
        return [
            'success' => true,
            'results' => $results,
            'total' => count($texts),
        ];
    }
}
