<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ia')]
class AdminIaController extends AbstractController
{
    #[Route('/', name: 'admin.ia.index')]
    public function index(): Response
    {
        return $this->render('admin/ia/index.html.twig');
    }
}
