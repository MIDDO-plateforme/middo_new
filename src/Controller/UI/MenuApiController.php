<?php

namespace App\Controller\UI;

use App\UI\Menu\MenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MenuApiController extends AbstractController
{
    #[Route('/api/ui/menu', name: 'api_ui_menu', methods: ['GET'])]
    public function menu(MenuService $menuService): JsonResponse
    {
        return $this->json($menuService->getMenu());
    }
}
