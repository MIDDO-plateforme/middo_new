<?php

namespace App\Controller\OS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/os')]
final class DesktopController extends AbstractController
{
    #[Route('/desktop', name: 'os_desktop')]
    public function desktop(): Response
    {
        return $this->render('os/desktop.html.twig', [
            'apps' => [
                [
                    'id' => 'documents',
                    'name' => 'Documents',
                    'icon' => '📁',
                    'route' => 'user_documents',
                ],
                [
                    'id' => 'notifications',
                    'name' => 'Notifications',
                    'icon' => '🔔',
                    'route' => 'user_notifications',
                ],
                [
                    'id' => 'ia_admin',
                    'name' => 'IA Admin',
                    'icon' => '🧭',
                    'route' => 'ia_admin_page',
                ],
                [
                    'id' => 'ia_translate',
                    'name' => 'Traduction',
                    'icon' => '🌍',
                    'route' => 'ia_translate_page',
                ],
            ],
        ]);
    }
}
