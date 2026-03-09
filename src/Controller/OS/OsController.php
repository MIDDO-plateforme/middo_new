<?php

namespace App\Controller\OS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/os')]
final class OsController extends AbstractController
{
    #[Route('', name: 'os_home')]
    public function index(): Response
    {
        return $this->render('os/index.html.twig', [
            'modules' => [
                [
                    'name' => 'Documents',
                    'icon' => '📁',
                    'route' => 'user_documents',
                    'description' => 'Gérer et analyser vos documents avec l’IA.',
                ],
                [
                    'name' => 'Notifications',
                    'icon' => '🔔',
                    'route' => 'user_notifications',
                    'description' => 'Suivre les événements importants de MIDDO.',
                ],
                [
                    'name' => 'IA Admin',
                    'icon' => '🧭',
                    'route' => 'ia_admin_page',
                    'description' => 'Piloter les pipelines et préférences IA.',
                ],
                [
                    'name' => 'IA Traduction',
                    'icon' => '🌍',
                    'route' => 'ia_translate_page',
                    'description' => 'Traduire et adapter vos contenus.',
                ],
                [
                    'name' => 'IA Éducation',
                    'icon' => '📘',
                    'route' => 'ia_educate_page',
                    'description' => 'Apprendre, expliquer, vulgariser.',
                ],
                [
                    'name' => 'IA Émotion',
                    'icon' => '💬',
                    'route' => 'ia_emotion_page',
                    'description' => 'Accompagner, reformuler, soutenir.',
                ],
            ],
        ]);
    }
}
