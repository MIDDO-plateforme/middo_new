<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FreelancerController extends AbstractController
{
    #[Route('/freelancer/profile/{id}', name: 'app_freelancer_profile')]
    public function profile(int $id = 1): Response
    {
        // Données de démonstration (plus tard on utilisera la base de données)
        $freelancer = [
            'id' => $id,
            'name' => 'Thomas Dubois',
            'title' => 'Développeur Web Full Stack & Expert WordPress',
            'location' => 'Lyon, France',
            'member_since' => 'Janvier 2023',
            'rating' => 4.8,
            'reviews_count' => 37,
            'completion_rate' => 94,
            'projects_completed' => 52,
            'hourly_rate' => 45,
            'bio' => "Développeur web passionné avec plus de 7 ans d'expérience, je me spécialise dans le développement d'applications web et de sites e-commerce performants. Expert en WordPress, je propose également mes services pour le développement Frontend et Backend.",
            'skills' => [
                'frontend' => [
                    ['name' => 'HTML/CSS', 'level' => 95],
                    ['name' => 'JavaScript', 'level' => 90],
                    ['name' => 'React', 'level' => 85],
                    ['name' => 'Vue.js', 'level' => 80],
                ],
                'backend' => [
                    ['name' => 'PHP', 'level' => 92],
                    ['name' => 'MySQL', 'level' => 88],
                    ['name' => 'WordPress', 'level' => 95],
                    ['name' => 'Laravel', 'level' => 85],
                ],
                'technologies' => ['WordPress', 'WooCommerce', 'PHP', 'MySQL', 'HTML5', 'CSS3', 'JavaScript', 'jQuery', 'React', 'Vue.js', 'Laravel', 'Bootstrap', 'Tailwind CSS', 'Git', 'REST API', 'Responsive Design']
            ],
            'languages' => ['Français', 'Anglais'],
            'availability' => '15 mars 2025',
            'response_time' => '2 heures'
        ];

        return $this->render('freelancer/profile.html.twig', [
            'freelancer' => $freelancer,
        ]);
    }

    #[Route('/freelancer', name: 'app_freelancer')]
    public function index(): Response
    {
        return $this->render('freelancer/index.html.twig', [
            'controller_name' => 'FreelancerController',
        ]);
    }
}