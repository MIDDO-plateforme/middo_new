<?php

namespace App\Service\Payment;

interface PaymentGatewayInterface
{
    public function charge(float $amount, array $options = []): array;
}
