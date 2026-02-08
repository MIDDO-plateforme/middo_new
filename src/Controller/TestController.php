<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test-ai', name: 'app_test_ai')]
    public function testAi(): Response
    {
        return $this->render('test/test-ai.html.twig');
    }
}