<?php

namespace App\Controller;

use App\Service\MenuBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Repository\ProjectRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
        #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        CacheInterface $cache,
        ProjectRepository $projectRepository,
        NotificationRepository $notificationRepository
    ): Response {
        // Cache des statistiques pour 5 minutes
        $stats = $cache->get('dashboard_stats_user_' . $this->getUser()->getId(), 
            function (ItemInterface $item) use ($projectRepository, $notificationRepository) {
                $item->expiresAfter(300); // 5 minutes
                
                return [
                    'projects_count' => $projectRepository->count(['owner' => $this->getUser()]),
                    'missions_count' => 5, // TODO: Implémenter avec MissionRepository
                    'messages_unread' => $notificationRepository->count([
                        'user' => $this->getUser(), 
                        'isRead' => false
                    ]),
                    'success_rate' => 68, // TODO: Calculer dynamiquement
                    'revenue' => 7000, // TODO: Implémenter avec système de paiement
                ];
            }
        );
        
        // Récupérer les 5 projets les plus récents
        $projects = $projectRepository->findBy(
            ['owner' => $this->getUser()],
            ['createdAt' => 'DESC'],
            5
        );
        
        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'projects' => $projects,
        ]);
    }
}