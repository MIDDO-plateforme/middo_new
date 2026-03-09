<?php

namespace App\Controller\System;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/system/logs')]
class SystemLogsController extends AbstractController
{
    #[Route('/', name: 'system.logs.index')]
    public function index(): Response
    {
        return new Response('System Logs Controller OK');
    }
}
