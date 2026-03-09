<?php

namespace App\Controller\IA;

use App\IA\Pipeline\PaymentPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ia/payment')]
final class PaymentIAController extends AbstractController
{
    public function __construct(
        private readonly PaymentPipeline $pipeline
    ) {}

    #[Route('/invoice', name: 'api_ia_payment_invoice', methods: ['POST'])]
    public function invoice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['text'])) {
            return $this->json(['error' => 'Missing field: text'], 400);
        }

        $response = $this->pipeline->analyzeInvoice($data['text']);

        return $this->json([
            'content' => $response->getContent(),
            'provider' => $response->getProvider(),
            'model' => $response->getModel(),
            'tokens' => $response->getTokens(),
        ]);
    }
}
