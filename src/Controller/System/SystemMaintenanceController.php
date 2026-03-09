<?php

namespace App\Controller\System;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/system/maintenance')]
class SystemMaintenanceController extends AbstractController
{
    #[Route('/', name: 'system.maintenance.index')]
    public function index(): Response
    {
        return new Response('System Maintenance Controller OK');
    }
}
