<?php

namespace App\Service\Payment;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Event;

class StripePaymentService
{
    public function __construct(
        private string $stripeSecretKey,
        private string $stripeWebhookSecret
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Crée un PaymentIntent Stripe.
     */
    public function createPaymentIntent(int $amount, string $currency = 'eur'): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'automatic_payment_methods' => ['enabled' => true],
        ]);
    }

    /**
     * Confirme un PaymentIntent existant.
     */
    public function confirmPayment(string $paymentIntentId): array
    {
        $intent = PaymentIntent::retrieve($paymentIntentId);
        $intent->confirm();

        return [
            'id' => $intent->id,
            'status' => $intent->status,
            'amount' => $intent->amount,
            'currency' => $intent->currency,
        ];
    }

    /**
     * Gère un webhook Stripe.
     */
    public function handleWebhook(string $payload, ?string $signature): array
    {
        try {
            $event = Event::constructFrom(json_decode($payload, true));

            return [
                'success' => true,
                'event_type' => $event->type,
                'data' => $event->data->object,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
