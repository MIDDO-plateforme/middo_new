<?php

namespace App\Controller\Api;

use OpenAI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/ia', name: 'api_ia_')]
class IaController extends AbstractController
{
    private function clientOpenAI()
    {
        return OpenAI::client($_ENV['OPENAI_API_KEY']);
    }

    private function clientAnthropic(): HttpClientInterface
    {
        return $this->container->get('http_client')->withOptions([
            'headers' => [
                'x-api-key' => $_ENV['ANTHROPIC_API_KEY'],
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json'
            ]
        ]);
    }

    private function askOpenAI(string $system, string $user): string
    {
        $response = $this->clientOpenAI()->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user]
            ]
        ]);

        return $response->choices[0]->message->content ?? "Réponse vide.";
    }

    private function askAnthropic(string $system, string $user): string
    {
        $client = $this->clientAnthropic();

        $response = $client->request('POST', 'https://api.anthropic.com/v1/messages', [
            'json' => [
                'model' => 'claude-3-sonnet-20240229',
                'max_tokens' => 800,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user]
                ]
            ]
        ]);

        $data = $response->toArray();

        return $data['content'][0]['text'] ?? "Réponse vide.";
    }

    // ============================================================
    // MODULE : SUGGESTIONS IA
    // ============================================================
    #[Route('/suggestions', name: 'suggestions', methods: ['POST'])]
    public function suggestions(Request $request): JsonResponse
    {
        $text = json_decode($request->getContent(), true)['text'] ?? null;
        if (!$text) return new JsonResponse(['error' => 'Missing text'], 400);

        $result = $this->askOpenAI(
            "Tu es une IA experte en analyse et recommandations professionnelles.",
            "Génère des suggestions utiles basées sur : $text"
        );

        return new JsonResponse(['result' => $result]);
    }

    // ============================================================
    // MODULE : RÉSUMÉ IA
    // ============================================================
    #[Route('/summary', name: 'summary', methods: ['POST'])]
    public function summary(Request $request): JsonResponse
    {
        $text = json_decode($request->getContent(), true)['text'] ?? null;
        if (!$text) return new JsonResponse(['error' => 'Missing text'], 400);

        $result = $this->askOpenAI(
            "Tu es une IA experte en synthèse. Résume clairement et efficacement.",
            "Résumé demandé : $text"
        );

        return new JsonResponse(['result' => $result]);
    }

    // ============================================================
    // MODULE : ANALYSE DES RISQUES
    // ============================================================
    #[Route('/risks', name: 'risks', methods: ['POST'])]
    public function risks(Request $request): JsonResponse
    {
        $text = json_decode($request->getContent(), true)['text'] ?? null;
        if (!$text) return new JsonResponse(['error' => 'Missing text'], 400);

        // Ici on utilise CLAUDE (Anthropic) car il est excellent en analyse
        $result = $this->askAnthropic(
            "Tu es une IA experte en analyse des risques juridiques, financiers et opérationnels.",
            "Analyse les risques suivants : $text"
        );

        return new JsonResponse(['result' => $result]);
    }

    // ============================================================
    // MODULE : GÉNÉRATION DE DOCUMENTS
    // ============================================================
    #[Route('/docgen', name: 'docgen', methods: ['POST'])]
    public function docgen(Request $request): JsonResponse
    {
        $text = json_decode($request->getContent(), true)['text'] ?? null;
        if (!$text) return new JsonResponse(['error' => 'Missing text'], 400);

        // Ici on utilise OpenAI (plus créatif)
        $result = $this->askOpenAI(
            "Tu es une IA experte en rédaction de documents professionnels.",
            "Génère un document professionnel basé sur : $text"
        );

        return new JsonResponse(['result' => $result]);
    }
}
