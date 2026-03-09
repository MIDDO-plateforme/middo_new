<?php

namespace App\Controller\IA;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/admin')]
final class AdminPageController extends AbstractController
{
    #[Route('', name: 'ia_admin_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ia/admin.html.twig');
    }
}
