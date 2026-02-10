<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IaTemplatesController extends AbstractController
{
    #[Route('/ia/templates', name: 'ia_templates')]
    public function templates(): Response
    {
        return $this->render('ia/templates.html.twig');
    }
}
