<?php

namespace App\Controller\OS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/os/settings')]
class OSSettingsController extends AbstractController
{
    #[Route('/', name: 'os.settings.index')]
    public function index(): Response
    {
        return new Response('OS Settings Controller OK');
    }
}
