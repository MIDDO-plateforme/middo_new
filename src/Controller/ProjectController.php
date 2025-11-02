<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/project/{id}', name: 'app_project_detail')]
    public function detail(int $id = 1): Response
    {
        // Données de démonstration (plus tard on utilisera la base de données)
        $project = [
            'id' => $id,
            'title' => 'Développement plateforme de diagnostic IA pour clinique',
            'status' => 'Ouvert',
            'status_class' => 'bg-success',
            'category' => 'Développement Logiciel Médical',
            'published_date' => '28/01/2025',
            'views' => 245,
            'proposals_count' => 18,
            'description' => "Nous recherchons un développeur expérimenté pour créer une plateforme web complète de diagnostic médical assisté par intelligence artificielle. Cette solution innovante permettra aux médecins d'obtenir des analyses rapides et précises pour améliorer la prise en charge des patients.",
            'detailed_description' => "La plateforme devra intégrer des algorithmes de machine learning pour analyser les symptômes, les résultats d'examens médicaux et proposer des diagnostics différentiels. L'interface doit être intuitive, responsive et conforme aux normes de sécurité médicale (RGPD, confidentialité des données patients).",
            'features' => [
                'Interface web responsive pour ordinateurs et tablettes',
                'Module d\'analyse de symptômes avec algorithmes IA',
                'Intégration d\'imagerie médicale (rayons X, IRM, échographies)',
                'Gestion sécurisée des dossiers patients',
                'Tableau de bord statistiques pour le personnel médical',
                'Système d\'alertes et notifications',
                'Export de rapports médicaux en PDF',
                'Conformité RGPD et sécurité des données médicales',
                'API pour intégration avec systèmes existants',
                'Documentation technique complète et formation du personnel'
            ],
            'skills' => [
                'Python', 'TensorFlow', 'Machine Learning', 'Django/Flask',
                'React.js', 'Node.js', 'PostgreSQL', 'Docker',
                'API REST', 'Sécurité des données', 'RGPD'
            ],
            'budget_min' => 5000,
            'budget_max' => 8000,
            'deadline' => '30/06/2025',
            'deadline_months' => 5,
            'experience_level' => 'Expert',
            'client' => [
                'name' => 'Clinique Santé Plus',
                'avatar' => 'https://via.placeholder.com/150/667eea/ffffff?text=Clinique',
                'location' => 'Kinshasa, RD Congo',
                'rating' => 4.7,
                'reviews_count' => 83,
                'projects_published' => 23,
                'projects_completed' => 18,
                'member_since' => 'Mars 2024'
            ],
            'similar_projects' => [
                [
                    'title' => 'Application mobile télémédecine',
                    'budget' => '3 500 € - 5 500 €',
                    'description' => 'Création d\'une application mobile pour consultations à distance avec diagnostic IA...'
                ],
                [
                    'title' => 'Système de gestion hospitalière',
                    'budget' => '6 000 € - 10 000 €',
                    'description' => 'Développement d\'un ERP complet pour la gestion d\'un hôpital (patients, stocks, RH)...'
                ],
                [
                    'title' => 'Plateforme e-learning médical',
                    'budget' => '2 500 € - 4 000 €',
                    'description' => 'Formation en ligne pour personnel médical avec modules interactifs et certifications...'
                ]
            ]
        ];

        return $this->render('project/detail.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project', name: 'app_project')]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }
}