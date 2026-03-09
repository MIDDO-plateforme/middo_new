<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/home-legacy')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'home.legacy.index')]
    public function index(): Response
    {
        return new Response('Legacy Home Controller OK');
    }
}
