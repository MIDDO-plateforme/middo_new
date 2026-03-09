<?php

namespace App\Infrastructure\Http\Controller;

use App\Application\IA\Command\GenerateResponseCommand;
use App\Application\IA\Handler\GenerateResponseHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IAController extends AbstractController
{
    #[Route('/ia/generate', name: 'ia_generate', methods: ['POST'])]
    public function generate(
        Request $request,
        GenerateResponseHandler $handler
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['prompt']) || empty($data['prompt'])) {
            return new JsonResponse(['error' => 'Missing prompt'], 400);
        }

        $command = new GenerateResponseCommand(
            userId: $user->id,
            prompt: $data['prompt'],
            context: $data['context'] ?? []
        );

        $response = $handler($command);

        return new JsonResponse([
            'text' => $response->text,
            'tokensUsed' => $response->tokensUsed,
        ]);
    }
}
