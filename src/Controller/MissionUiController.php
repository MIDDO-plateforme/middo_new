<?php

namespace App\Controller;

use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissionUiController extends AbstractController
{
    #[Route('/missions', name: 'app_missions', methods: ['GET'])]
    public function search(Request $request, MissionRepository $missionRepository): Response
    {
        // TODO: Implémenter la recherche avec filtres
        $missions = $missionRepository->findAll();
        
        return $this->render('mission/search.html.twig', [
            'missions' => $missions,
        ]);
    }
}