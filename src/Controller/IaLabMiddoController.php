<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IaLabMiddoController extends AbstractController
{
    #[Route('/ia/lab', name: 'ia_lab')]
    public function index(): Response
    {
        return $this->render('ia/lab.html.twig');
    }
}
