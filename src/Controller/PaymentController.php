<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/api/payment/create-intent', name: 'api_payment_create', methods: ['POST'])]
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'] ?? 1000; // Montant en centimes
        $currency = $data['currency'] ?? 'eur';
        
        // Simulation Stripe Payment Intent
        $clientSecret = 'pi_' . bin2hex(random_bytes(16)) . '_secret_' . bin2hex(random_bytes(16));
        
        return $this->json([
            'success' => true,
            'client_secret' => $clientSecret,
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'status' => 'requires_payment_method',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    #[Route('/api/payment/confirm', name: 'api_payment_confirm', methods: ['POST'])]
    public function confirmPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $paymentIntentId = $data['payment_intent_id'] ?? '';
        
        // Simulation confirmation paiement
        return $this->json([
            'success' => true,
            'payment_intent_id' => $paymentIntentId,
            'status' => 'succeeded',
            'amount_received' => 1000,
            'currency' => 'EUR',
            'receipt_url' => 'https://middo.io/receipt/' . uniqid(),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    #[Route('/api/payment/escrow/lock', name: 'api_escrow_lock', methods: ['POST'])]
    public function lockEscrow(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'] ?? 0;
        $projectId = $data['project_id'] ?? uniqid();
        
        // Simulation ESCROW Blockchain Polygon
        $txHash = '0x' . bin2hex(random_bytes(32));
        $contractAddress = '0x' . bin2hex(random_bytes(20));
        
        return $this->json([
            'success' => true,
            'escrow_id' => uniqid('escrow_'),
            'transaction_hash' => $txHash,
            'contract_address' => $contractAddress,
            'amount' => $amount,
            'status' => 'locked',
            'blockchain' => 'Polygon',
            'network' => 'mainnet',
            'project_id' => $projectId,
            'locked_until' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'gas_fee' => '0.05 MATIC',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    #[Route('/api/payment/escrow/release', name: 'api_escrow_release', methods: ['POST'])]
    public function releaseEscrow(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $escrowId = $data['escrow_id'] ?? '';
        
        // Simulation libération ESCROW
        $txHash = '0x' . bin2hex(random_bytes(32));
        
        return $this->json([
            'success' => true,
            'escrow_id' => $escrowId,
            'transaction_hash' => $txHash,
            'status' => 'released',
            'released_to' => '0x' . bin2hex(random_bytes(20)),
            'amount' => 1000,
            'blockchain' => 'Polygon',
            'gas_fee' => '0.03 MATIC',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    #[Route('/api/payment/webhook', name: 'api_payment_webhook', methods: ['POST'])]
    public function handleWebhook(Request $request): JsonResponse
    {
        // Simulation webhook Stripe
        $payload = $request->getContent();
        
        return $this->json([
            'success' => true,
            'webhook_received' => true,
            'event_type' => 'payment_intent.succeeded',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}