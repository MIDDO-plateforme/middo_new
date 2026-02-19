<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/bank', name: 'bank_index')]
    public function index(): Response
    {
        return $this->render('bank/index.html.twig');
    }
}
