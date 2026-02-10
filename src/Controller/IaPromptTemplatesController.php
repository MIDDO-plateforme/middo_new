<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IaPromptTemplatesController extends AbstractController
{
    #[Route('/ia/prompts', name: 'ia_prompts')]
    public function index(): Response
    {
        return $this->render('ia/prompts.html.twig');
    }
}
