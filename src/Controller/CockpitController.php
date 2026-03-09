<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CockpitController extends AbstractController
{
    #[Route('/cockpit', name: 'app_cockpit')]
    public function index(): Response
    {
        return $this->render('cockpit/index.html.twig');
    }
}
