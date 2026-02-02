<?php

namespace App\Controller;

use App\Service\MenuBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(MenuBuilder $menuBuilder): Response
    {
        $menu = $menuBuilder->getMainMenu();
        return $this->render('dashboard/index.html.twig', ['menu' => $menu]);
    }
}