<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    #[Route('/company/profile/{id}', name: 'app_company_profile')]
    public function profile(int $id = 1): Response
    {
        // Données de démonstration (plus tard on utilisera la base de données)
        $company = [
            'id' => $id,
            'name' => 'Clinique Santé Plus',
            'type' => 'Clinique privée spécialisée en médecine générale et diagnostics avancés',
            'location' => 'Kinshasa, RD Congo',
            'member_since' => 'Mars 2024',
            'rating' => 4.7,
            'reviews_count' => 83,
            'completion_rate' => 96,
            'projects_published' => 127,
            'projects_active' => 15,
            'description' => "Clinique Santé Plus est un établissement médical innovant spécialisé dans l'utilisation de l'intelligence artificielle pour le diagnostic médical. Fondée en 2020, notre mission est d'offrir des services de santé de qualité accessible à tous en Afrique.",
            'description_long' => "Notre équipe pluridisciplinaire combine expertise médicale et technologies de pointe pour offrir des diagnostics précis et rapides. Du diagnostic IA automatisé à la télémédecine, en passant par les analyses de laboratoire avancées, nous proposons une gamme complète de services pour répondre aux besoins de nos patients.",
            'description_approach' => "Nous privilégions une approche holistique et centrée sur le patient, en utilisant les dernières innovations technologiques pour améliorer la qualité des soins. Notre objectif est de rendre la médecine de pointe accessible à tous.",
            'expertise_areas' => [
                'medical' => [
                    'title' => 'Services médicaux',
                    'description' => 'Consultations générales, diagnostics spécialisés, télémédecine, analyses de laboratoire.',
                    'technologies' => ['Diagnostic IA', 'Télémédecine', 'Analyses avancées', 'Imagerie médicale']
                ],
                'technology' => [
                    'title' => 'Technologies IA',
                    'description' => 'Diagnostic automatisé par IA, analyse prédictive, détection précoce de maladies.',
                    'technologies' => ['TensorFlow', 'PyTorch', 'Computer Vision', 'Machine Learning']
                ],
                'prevention' => [
                    'title' => 'Prévention & Suivi',
                    'description' => 'Programmes de prévention, suivi personnalisé, éducation à la santé.',
                    'technologies' => ['Suivi patient', 'Alertes préventives', 'Télé-suivi', 'Applications mobiles']
                ]
            ],
            'team' => [
                ['name' => 'Dr. Jean Kabamba', 'role' => 'Directeur médical', 'avatar' => 'https://via.placeholder.com/50'],
                ['name' => 'Dr. Marie Lukusa', 'role' => 'Chef de service diagnostics', 'avatar' => 'https://via.placeholder.com/50'],
                ['name' => 'Ing. Patrick Mbuyi', 'role' => 'Responsable IA & Technologies', 'avatar' => 'https://via.placeholder.com/50'],
                ['name' => 'Dr. Sophie Nzuzi', 'role' => 'Médecin généraliste', 'avatar' => 'https://via.placeholder.com/50'],
                ['name' => 'Ing. Thomas Kalala', 'role' => 'Développeur systèmes médicaux', 'avatar' => 'https://via.placeholder.com/50'],
                ['name' => 'Mme Claire Tshiani', 'role' => 'Responsable relations patients', 'avatar' => 'https://via.placeholder.com/50'],
            ],
            'active_projects' => [
                [
                    'title' => 'Programme de dépistage diabète',
                    'category' => 'Prévention & Santé publique',
                    'description' => 'Programme de dépistage précoce du diabète utilisant l\'IA pour analyser les facteurs de risque...',
                    'budget' => '2 500 € - 4 000 €',
                    'status' => 'Ouvert',
                    'status_class' => 'status-open',
                    'image' => 'https://via.placeholder.com/400x300'
                ],
                [
                    'title' => 'Plateforme télémédecine mobile',
                    'category' => 'Développement technologique',
                    'description' => 'Application mobile permettant des consultations à distance avec diagnostic IA intégré...',
                    'budget' => '3 500 € - 6 000 €',
                    'status' => 'En cours',
                    'status_class' => 'status-in-progress',
                    'image' => 'https://via.placeholder.com/400x300'
                ],
                [
                    'title' => 'Système de gestion des patients',
                    'category' => 'Infrastructure médicale',
                    'description' => 'Digitalisation complète du parcours patient avec dossier médical électronique sécurisé...',
                    'budget' => '4 000 € - 7 000 €',
                    'status' => 'Ouvert',
                    'status_class' => 'status-open',
                    'image' => 'https://via.placeholder.com/400x300'
                ],
                [
                    'title' => 'Formation personnel médical IA',
                    'category' => 'Formation & Développement',
                    'description' => 'Programme de formation du personnel aux outils de diagnostic assisté par IA...',
                    'budget' => '1 500 € - 2 500 €',
                    'status' => 'En cours',
                    'status_class' => 'status-in-progress',
                    'image' => 'https://via.placeholder.com/400x300'
                ]
            ],
            'contact' => [
                'address' => '123 Avenue de la Santé, Gombe, Kinshasa',
                'phone' => '+243 81 234 5678',
                'response_time' => '3 heures',
                'languages' => ['Français', 'Lingala', 'Anglais'],
                'website' => 'clinique-sante-plus.cd',
                'linkedin' => 'linkedin.com/company/clinique-sante-plus',
                'facebook' => 'facebook.com/cliniquesanteplus'
            ]
        ];

        return $this->render('company/profile.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/company', name: 'app_company')]
    public function index(): Response
    {
        return $this->render('company/index.html.twig', [
            'controller_name' => 'CompanyController',
        ]);
    }
}