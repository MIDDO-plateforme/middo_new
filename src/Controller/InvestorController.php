<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvestorController extends AbstractController
{
    #[Route('/investor', name: 'app_investor')]
    public function index(): Response
    {
        return $this->render('investor/index.html.twig', [
            'controller_name' => 'InvestorController',
        ]);
    }
}
