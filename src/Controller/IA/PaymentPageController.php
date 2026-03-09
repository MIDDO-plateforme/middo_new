<?php

namespace App\Controller\IA;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/payment')]
final class PaymentPageController extends AbstractController
{
    #[Route('', name: 'ia_payment_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ia/payment.html.twig');
    }
}
