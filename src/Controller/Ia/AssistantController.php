<?php

namespace App\Controller\Ia;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/assistant')]
class AssistantController extends AbstractController
{
    #[Route('/', name: 'ia.assistant.index')]
    public function index(): Response
    {
        return new Response('IA Assistant Controller OK');
    }
}
