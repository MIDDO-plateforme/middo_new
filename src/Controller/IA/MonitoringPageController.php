<?php

namespace App\Controller\IA;

use App\IA\Monitoring\MonitoringManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/monitoring')]
final class MonitoringPageController extends AbstractController
{
    #[Route('', name: 'ia_monitoring_page', methods: ['GET'])]
    public function index(MonitoringManager $monitor): Response
    {
        return $this->render('ia/monitoring.html.twig', [
            'stats' => $monitor->getStats(),
        ]);
    }
}
