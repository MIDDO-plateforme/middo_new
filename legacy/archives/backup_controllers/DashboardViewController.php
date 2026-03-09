<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardViewController extends AbstractController
{
    /**
     * Route pour afficher le dashboard HTML
     */
    #[Route('/dashboard', name: 'dashboard_view', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/dashboard.html.twig');
    }

    /**
     * Route alternative
     */
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->redirectToRoute('dashboard_view');
    }
}
