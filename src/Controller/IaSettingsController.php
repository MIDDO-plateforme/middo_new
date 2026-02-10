<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IaSettingsController extends AbstractController
{
    #[Route('/ia/settings', name: 'ia_settings')]
    public function settings(): Response
    {
        return $this->render('ia/settings.html.twig');
    }
}
