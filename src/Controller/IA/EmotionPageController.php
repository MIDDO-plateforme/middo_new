<?php

namespace App\Controller\IA;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/emotion')]
final class EmotionPageController extends AbstractController
{
    #[Route('', name: 'ia_emotion_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ia/emotion.html.twig');
    }
}
