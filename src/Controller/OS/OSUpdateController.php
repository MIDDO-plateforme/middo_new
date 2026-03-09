<?php

namespace App\Controller\OS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/os/update')]
class OSUpdateController extends AbstractController
{
    #[Route('/', name: 'os.update.index')]
    public function index(): Response
    {
        return new Response('OS Update Controller OK');
    }
}

