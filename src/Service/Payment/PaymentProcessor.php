<?php

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

class PaymentProcessor
{
    public function __construct(
        private EntityManagerInterface $em,
        private PaymentRepository $paymentRepository
    ) {}

    public function createPayment(int $userId, float $amount): Payment
    {
        $payment = new Payment();
        $payment->setUser($this->em->getReference('App\Entity\User', $userId));
        $payment->setAmount($amount);
        $payment->setStatus('pending');

        $this->em->persist($payment);
        $this->em->flush();

        return $payment;
    }

    public function markAsPaid(Payment $payment): void
    {
        $payment->setStatus('paid');
        $this->em->flush();
    }

    public function markAsFailed(Payment $payment): void
    {
        $payment->setStatus('failed');
        $this->em->flush();
    }
}
