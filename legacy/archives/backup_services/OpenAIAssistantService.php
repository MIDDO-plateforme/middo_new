<?php

namespace App\Service;

use App\Entity\AIInteraction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OpenAI;

class OpenAIAssistantService implements AIAssistantInterface
{
    private $client;
    private EntityManagerInterface $em;
    private string $model;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
        $this->model = $_ENV['OPENAI_MODEL'] ?? 'gpt-3.5-turbo';
        
        if (empty($apiKey)) {
            throw new \RuntimeException('OPENAI_API_KEY non configuree');
        }

        $this->client = OpenAI::client($apiKey);
    }

    public function generateResponse(string $prompt, ?array $context = null): string
    {
        try {
            $userId = $context['userId'] ?? null;

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant IA pour MIDDO.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 500
            ]);

            $aiResponse = $response->choices[0]->message->content;
            $this->logInteraction('chat', $prompt, $aiResponse, $userId);

            return $aiResponse;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur OpenAI: ' . $e->getMessage());
        }
    }

    public function suggestProjectImprovements(string $projectDescription, array $projectData): string
    {
        try {
            $prompt = "Analyse ce projet et propose 3 ameliorations:\n\n";
            $prompt .= "Titre: " . ($projectData['title'] ?? '') . "\n";
            $prompt .= "Budget: " . ($projectData['budget'] ?? '') . "\n";
            $prompt .= "Statut: " . ($projectData['status'] ?? '') . "\n";
            $prompt .= "Description: " . $projectDescription . "\n\n";
            $prompt .= "Liste 3 suggestions numerotees.";

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Expert en gestion de projet.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 700
            ]);

            $suggestions = $response->choices[0]->message->content;
            $this->logInteraction('suggest', $prompt, $suggestions);

            return $suggestions;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur: ' . $e->getMessage());
        }
    }

    public function matchUsersToProject(string $projectDescription, array $projectData): string
    {
        try {
            $users = $this->em->getRepository(User::class)->findAll();

            if (empty($users)) {
                return "Aucun utilisateur.";
            }

            $profiles = [];
            foreach ($users as $user) {
                $profiles[] = [
                    'nom' => $user->getFirstName() . ' ' . $user->getLastName(),
                    'email' => $user->getEmail(),
                    'expertise' => $user->getDomaineExpertise() ?? 'Non specifie',
                    'competences' => $user->getCeQueVousSavezFaire() ?? 'Non specifie'
                ];
            }

            $prompt = "Recommande 3 profils:\n\n";
            $prompt .= "PROJET: " . ($projectData['title'] ?? '') . "\n";
            $prompt .= "Description: " . $projectDescription . "\n\n";
            $prompt .= "PROFILS:\n" . json_encode($profiles, JSON_UNESCAPED_UNICODE);

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Expert matching.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 800
            ]);

            $matches = $response->choices[0]->message->content;
            $this->logInteraction('match', $prompt, $matches);

            return $matches;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur: ' . $e->getMessage());
        }
    }

    public function analyzeSentiment(string $text): array
    {
        try {
            $prompt = "Analyse le sentiment et le ton de ce texte de projet. ";
            $prompt .= "Donne un score de 0 à 100 (0=très négatif, 100=très positif).\n\n";
            $prompt .= "Texte: " . $text . "\n\n";
            $prompt .= "Réponds au format: Score: XX/100 puis explique ton analyse.";

            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un expert en analyse de sentiment et de ton pour des descriptions de projets professionnels.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7
            ]);

            $aiResponse = $response->choices[0]->message->content;
            $this->logInteraction('sentiment', $prompt, $aiResponse);

            // Extraction du score
            preg_match('/(\d+)/', $aiResponse, $matches);
            $score = isset($matches[0]) ? (int)$matches[0] : 50;
            
            // Limite le score entre 0 et 100
            $score = max(0, min(100, $score));

            // Détermination du label
            if ($score >= 80) {
                $label = 'Très Positif';
            } elseif ($score >= 60) {
                $label = 'Positif';
            } elseif ($score >= 40) {
                $label = 'Neutre';
            } elseif ($score >= 20) {
                $label = 'Négatif';
            } else {
                $label = 'Très Négatif';
            }

            // Recommandations selon le score
            if ($score >= 80) {
                $recommendations = 'Excellent ! Continuez ainsi. Votre projet dégage un ton très positif et engageant.';
            } elseif ($score >= 60) {
                $recommendations = 'Bon sentiment général. Quelques améliorations mineures pourraient renforcer l\'impact.';
            } elseif ($score >= 40) {
                $recommendations = 'Ton neutre. Ajoutez plus d\'enthousiasme, de clarté ou de bénéfices concrets pour engager davantage.';
            } elseif ($score >= 20) {
                $recommendations = 'Sentiment négatif détecté. Reformulez pour mettre en avant les aspects positifs et les opportunités.';
            } else {
                $recommendations = 'Ton très négatif. Revoyez complètement la description pour la rendre plus attractive et motivante.';
            }

            return [
                'score' => $score,
                'label' => $label,
                'content' => $aiResponse,
                'recommendations' => $recommendations
            ];

        } catch (\Exception $e) {
            return [
                'score' => 0,
                'label' => 'Erreur',
                'content' => '',
                'recommendations' => '',
                'error' => 'Erreur lors de l\'analyse : ' . $e->getMessage()
            ];
        }
    }

    private function logInteraction(string $type, string $prompt, string $response, ?int $userId = null): void
    {
        try {
            $interaction = new AIInteraction();
            $interaction->setInteractionType($type);
            $interaction->setPrompt($prompt);
            $interaction->setResponse($response);
            $interaction->setCreatedAt(new \DateTimeImmutable());

            if ($userId) {
                $user = $this->em->getRepository(User::class)->find($userId);
                if ($user) $interaction->setUser($user);
            }

            $this->em->persist($interaction);
            $this->em->flush();
        } catch (\Exception $e) {
        }
    }
}
