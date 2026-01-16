<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted('ROLE_USER')]
class RoleSwitchController extends AbstractController
{
    #[Route('/switch-role', name: 'app_switch_role', methods: ['POST'])]
    public function switchRole(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newRole = $data['role'] ?? null;

        $allowedRoles = [
            'particulier',
            'entrepreneur',
            'entreprise',
            'inspecteur',
            'formateur',
            'partenaire'
        ];

        if (!$newRole || !in_array($newRole, $allowedRoles)) {
            return $this->json([
                'success' => false,
                'message' => 'Rôle invalide'
            ], 400);
        }

        // Stocker le rôle actuel en session
        $session->set('current_role', $newRole);

        return $this->json([
            'success' => true,
            'role' => $newRole,
            'message' => 'Rôle changé avec succès'
        ]);
    }

    #[Route('/current-role', name: 'app_current_role', methods: ['GET'])]
    public function getCurrentRole(SessionInterface $session): JsonResponse
    {
        $currentRole = $session->get('current_role', 'particulier');

        return $this->json([
            'success' => true,
            'role' => $currentRole
        ]);
    }
}
