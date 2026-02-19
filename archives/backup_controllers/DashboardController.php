<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard_view')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        // Données de démonstration pour SESSION 58
        $stats = [
            'total_projects' => 12,
            'active_projects' => 8,
            'completed_projects' => 4,
            'total_tasks' => 45,
            'completed_tasks' => 18,
            'pending_tasks' => 27,
            'total_messages' => 127,
            'unread_messages' => 8,
            'team_members' => 15,
            'notifications' => 3
        ];
        
        // Sessions Timeline (60 sessions planifiées)
        $sessions = [];
        for ($i = 1; $i <= 60; $i++) {
            $sessions[] = [
                'id' => $i,
                'number' => $i,
                'title' => $this->getSessionTitle($i),
                'status' => $i < 58 ? 'completed' : ($i === 58 ? 'in-progress' : 'upcoming'),
                'date' => $this->getSessionDate($i),
                'duration' => '2H00',
                'progress' => $i < 58 ? 100 : ($i === 58 ? 95 : 0)
            ];
        }
        
        // Activités récentes
        $recentActivities = [
            [
                'type' => 'task_completed',
                'title' => 'Tâche terminée',
                'description' => 'Intégration des 4 graphiques PNG complétée',
                'user' => 'Baudouin',
                'time' => '5 min',
                'icon' => 'check-circle-fill',
                'color' => 'success'
            ],
            [
                'type' => 'message',
                'title' => 'Nouveau message',
                'description' => 'Assistant IA : "Dashboard validé à 100%"',
                'user' => 'Assistant IA',
                'time' => '12 min',
                'icon' => 'chat-dots-fill',
                'color' => 'info'
            ],
            [
                'type' => 'member',
                'title' => 'Nouveau membre',
                'description' => 'Marie Dupont a rejoint l\'équipe',
                'user' => 'Système',
                'time' => '1h',
                'icon' => 'person-plus-fill',
                'color' => 'primary'
            ],
            [
                'type' => 'analytics',
                'title' => 'Mise à jour Analytics',
                'description' => 'Rapports mensuels générés',
                'user' => 'Système',
                'time' => '2h',
                'icon' => 'bar-chart-fill',
                'color' => 'warning'
            ],
            [
                'type' => 'email',
                'title' => 'Email Dashboard envoyé',
                'description' => 'Rapport hebdomadaire envoyé à mbane.baudouin@gmail.com',
                'user' => 'Système',
                'time' => '3h',
                'icon' => 'envelope-fill',
                'color' => 'secondary'
            ]
        ];
        
        return $this->render('dashboard/dashboard.html.twig', [
            'user' => $user,
            'stats' => $stats,
            'sessions' => $sessions,
            'current_session' => 58,
            'total_sessions' => 60,
            'overall_progress' => 57, // 34/60 sessions complétées
            'recent_activities' => $recentActivities,
            'charts_available' => true, // Indicateur pour afficher les graphiques PNG
        ]);
    }
    
    private function getSessionTitle(int $sessionNumber): string
    {
        $titles = [
            1 => 'Initialisation Projet MIDDO',
            2 => 'Architecture Symfony 6.3',
            3 => 'Système d\'authentification',
            4 => 'Entités & Base de données',
            5 => 'UI/UX Design System',
            10 => 'Module Messages & Chat',
            15 => 'Module Projets',
            20 => 'APIs IA - Intégration',
            25 => 'Système de notifications',
            30 => 'Dashboard Analytics',
            35 => 'Module Recherche Avancée',
            40 => 'Tests & Optimisations',
            45 => 'Sécurité & RGPD',
            50 => 'Performance & Cache',
            55 => 'Documentation Complète',
            58 => 'Onboarding Flow & Navigation Complète',
            60 => 'Déploiement Production'
        ];
        
        return $titles[$sessionNumber] ?? "Session $sessionNumber - Développement";
    }
    
    private function getSessionDate(int $sessionNumber): string
    {
        $startDate = new \DateTime('2025-01-01');
        $startDate->modify('+' . ($sessionNumber - 1) . ' days');
        
        if ($sessionNumber === 58) {
            return '18 Déc 2025'; // Session en cours
        }
        
        return $startDate->format('d M Y');
    }
}