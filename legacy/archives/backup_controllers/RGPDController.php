<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rgpd', name: 'rgpd_')]
class RGPDController extends AbstractController
{
    #[Route('/export', name: 'export', methods: ['GET'])]
    public function exportData(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non authentifie'], 401);
        }
        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ]);
    }
    
    #[Route('/privacy', name: 'privacy')]
    public function privacy(): Response
    {
        return $this->render('rgpd/privacy.html.twig');
    }
}