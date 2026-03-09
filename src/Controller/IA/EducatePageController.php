<?php

namespace App\Controller\IA;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/educate')]
final class EducatePageController extends AbstractController
{
    #[Route('', name: 'ia_educate_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ia/educate.html.twig');
    }
}
