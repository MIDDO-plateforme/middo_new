<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IaAutomationsController extends AbstractController
{
    #[Route('/ia/automations', name: 'ia_automations')]
    public function automations(): Response
    {
        return $this->render('ia/automations.html.twig');
    }
}
