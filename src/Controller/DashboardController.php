<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard-legacy')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard.legacy.index')]
    public function index(): Response
    {
        return new Response('Legacy Dashboard Controller OK');
    }
}
