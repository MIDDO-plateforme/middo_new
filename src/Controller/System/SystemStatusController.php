<?php

namespace App\Controller\System;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/system/status')]
class SystemStatusController extends AbstractController
{
    #[Route('/', name: 'system.status.index')]
    public function index(): Response
    {
        return new Response('System Status Controller OK');
    }
}
