<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatchingFrontendController extends AbstractController
{
    #[Route('/discover-projects', name: 'app_discover_projects')]
    public function discoverProjects(): Response
    {
        return $this->render('matching/discover_projects.html.twig');
    }

    #[Route('/project-compatibility', name: 'app_project_compatibility')]
    public function projectCompatibility(): Response
    {
        return $this->render('matching/project_compatibility.html.twig');
    }

    #[Route('/skill-development', name: 'app_skill_development')]
    public function skillDevelopment(): Response
    {
        return $this->render('matching/skill_development.html.twig');
    }

    #[Route('/find-collaborators', name: 'app_find_collaborators')]
    public function findCollaborators(): Response
    {
        return $this->render('matching/find_collaborators.html.twig');
    }

    
    #[Route('/dashboard-analytics', name: 'app_dashboard_analytics')]
    public function dashboardAnalytics(): Response
    {
        return $this->render('matching/dashboard_analytics.html.twig');
    }

    #[Route('/favorites', name: 'app_favorites')]
    public function favorites(): Response
    {
        return $this->render('matching/favorites.html.twig');
    }
}
