<?php

namespace App\Controller\IA;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ia/user')]
final class UserPageController extends AbstractController
{
    #[Route('', name: 'ia_user_page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('ia/user.html.twig', [
            'userData' => [
                'id' => $this->getUser()?->getUserIdentifier(),
                'email' => $this->getUser()?->getUserIdentifier(),
            ],
        ]);
    }
}
