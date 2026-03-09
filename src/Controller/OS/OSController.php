<?php

namespace App\Controller\OS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/os')]
class OSController extends AbstractController
{
    #[Route('/', name: 'os.index')]
    public function index(): Response
    {
        return new Response('OS Controller OK');
    }
}
