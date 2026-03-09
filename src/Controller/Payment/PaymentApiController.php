<?php

namespace App\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment/api')]
class PaymentApiController extends AbstractController
{
    #[Route('/', name: 'payment.api.index')]
    public function index(): Response
    {
        return new Response('Payment API Controller OK');
    }
}
