<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class AdminUsersController extends AbstractController
{
    #[Route('/', name: 'admin.users.index')]
    public function index(): Response
    {
        return $this->render('admin/users/index.html.twig');
    }
}
