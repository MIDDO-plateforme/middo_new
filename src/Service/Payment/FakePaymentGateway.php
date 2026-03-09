<?php

namespace App\Service\Payment;

class FakePaymentGateway implements PaymentGatewayInterface
{
    public function charge(float $amount, array $options = []): array
    {
        return [
            'success' => true,
            'transaction_id' => 'FAKE-' . uniqid(),
            'amount' => $amount,
        ];
    }
}
