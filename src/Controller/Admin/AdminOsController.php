<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/os')]
class AdminOsController extends AbstractController
{
    #[Route('/', name: 'admin.os.index')]
    public function index(): Response
    {
        return $this->render('admin/os/index.html.twig');
    }
}
