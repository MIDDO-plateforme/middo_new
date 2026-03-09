<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/settings')]
class AdminSettingsController extends AbstractController
{
    #[Route('/', name: 'admin.settings.index')]
    public function index(): Response
    {
        return $this->render('admin/settings/index.html.twig');
    }
}
