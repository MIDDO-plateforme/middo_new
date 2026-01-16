<?php

namespace App\Controller\Admin;

use App\Service\SOIA\CircuitBreakerService;
use App\Service\SOIA\Fallback\FallbackManager;
use App\Service\SOIA\HealthCheck\HealthCheckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/soia', name: 'admin_soia_')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('admin/soia_dashboard.html.twig', [
            'title' => 'SOIA Monitoring Dashboard'
        ]);
    }
}