<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IaDashboardController extends AbstractController
{
    #[Route('/ia/dashboard', name: 'ia_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('ia/dashboard.html.twig');
    }
}
