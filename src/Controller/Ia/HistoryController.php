<?php

namespace App\Controller\Ia;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/history')]
class HistoryController extends AbstractController
{
    #[Route('/', name: 'ia.history.index')]
    public function index(): Response
    {
        return new Response('IA History Controller OK');
    }
}
