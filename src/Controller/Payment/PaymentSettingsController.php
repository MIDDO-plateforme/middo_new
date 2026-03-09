<?php

namespace App\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment/settings')]
class PaymentSettingsController extends AbstractController
{
    #[Route('/', name: 'payment.settings.index')]
    public function index(): Response
    {
        return new Response('Payment Settings Controller OK');
    }
}
