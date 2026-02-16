<?php

namespace App\Controller;

use App\Service\StripePaymentService;
use App\Service\EscrowService;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private StripePaymentService $stripeService,
        private EscrowService $escrowService,
        private PaymentRepository $paymentRepository
    ) {}

    #[Route('/api/payment/intent', name: 'api_payment_intent', methods: ['POST'])]
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $amount = $data['amount'] ?? null;
        $currency = $data['currency'] ?? 'eur';

        if (!$amount) {
            return $this->json(['error' => 'Amount is required'], 400);
        }

        $intent = $this->stripeService->createPaymentIntent($amount, $currency);

        return $this->json([
            'success' => true,
            'client_secret' => $intent->client_secret,
            'payment_intent_id' => $intent->id,
        ]);
    }

    #[Route('/api/payment/confirm', name: 'api_payment_confirm', methods: ['POST'])]
    public function confirmPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $paymentIntentId = $data['payment_intent_id'] ?? null;

        if (!$paymentIntentId) {
            return $this->json(['error' => 'payment_intent_id is required'], 400);
        }

        $payment = $this->stripeService->confirmPayment($paymentIntentId);

        return $this->json([
            'success' => true,
            'payment' => $payment,
        ]);
    }

    #[Route('/api/payment/webhook', name: 'api_payment_webhook', methods: ['POST'])]
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->headers->get('stripe-signature');

        $result = $this->stripeService->handleWebhook($payload, $signature);

        return $this->json($result);
    }

    #[Route('/api/payment/escrow/lock', name: 'api_escrow_lock', methods: ['POST'])]
    public function lockEscrow(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $amount = $data['amount'] ?? null;
        $projectId = $data['project_id'] ?? null;

        if (!$amount || !$projectId) {
            return $this->json(['error' => 'amount and project_id are required'], 400);
        }

        $escrow = $this->escrowService->lockEscrow($amount, $projectId);

        return $this->json([
            'success' => true,
            'escrow' => $escrow,
        ]);
    }

    #[Route('/api/payment/escrow/release', name: 'api_escrow_release', methods: ['POST'])]
    public function releaseEscrow(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $escrowId = $data['escrow_id'] ?? null;

        if (!$escrowId) {
            return $this->json(['error' => 'escrow_id is required'], 400);
        }

        $release = $this->escrowService->releaseEscrow($escrowId);

        return $this->json([
            'success' => true,
            'release' => $release,
        ]);
    }

    #[Route('/api/payment/{id}', name: 'api_payment_get', methods: ['GET'])]
    public function getPayment(string $id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return $this->json(['error' => 'Payment not found'], 404);
        }

        return $this->json($payment);
    }

    #[Route('/api/payment/user/{id}', name: 'api_payment_user', methods: ['GET'])]
    public function getUserPayments(string $id): JsonResponse
    {
        $payments = $this->paymentRepository->findBy(['user' => $id]);

        return $this->json($payments);
    }
}


