<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MiddoController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/banque', name: 'app_banque')]
    public function banque(): Response
    {
        return $this->render('banque/index.html.twig');
    }

    #[Route('/travail', name: 'app_travail')]
    public function travail(): Response
    {
        return $this->render('travail/index.html.twig');
    }

    #[Route('/projets', name: 'app_projets')]
    public function projets(): Response
    {
        return $this->render('projets/index.html.twig');
    }

    #[Route('/messages', name: 'app_messages')]
    public function messages(): Response
    {
        return $this->render('messages/index.html.twig');
    }

    #[Route('/annuaire', name: 'app_annuaire')]
    public function annuaire(): Response
    {
        return $this->render('annuaire/index.html.twig');
    }

    #[Route('/transactions', name: 'app_transactions')]
    public function transactions(): Response
    {
        return $this->render('transactions/index.html.twig');
    }

    #[Route('/visio', name: 'app_visio')]
    public function visio(): Response
    {
        return $this->render('visio/index.html.twig');
    }

    #[Route('/analytics', name: 'app_analytics')]
    public function analytics(): Response
    {
        return $this->render('analytics/index.html.twig');
    }

    #[Route('/parametres', name: 'app_parametres')]
    public function parametres(): Response
    {
        return $this->render('parametres/index.html.twig');
    }
}