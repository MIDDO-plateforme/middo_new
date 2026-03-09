<?php

namespace App\Controller\IA;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/translate')]
final class TranslatePageController extends AbstractController
{
    #[Route('', name: 'ia_translate_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ia/translate.html.twig');
    }
}
