<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IaLogsController extends AbstractController
{
    #[Route('/ia/logs', name: 'ia_logs')]
    public function logs(): Response
    {
        return $this->render('ia/logs.html.twig');
    }
}
