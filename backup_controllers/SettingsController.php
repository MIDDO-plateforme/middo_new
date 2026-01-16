<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/analytics', name: 'analytics_index')]
    public function index(): Response
    {
        return $this->render('analytics/index.html.twig');
    }
}
