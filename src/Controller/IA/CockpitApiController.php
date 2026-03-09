<?php

namespace App\Controller\IA;

use App\IA\Cockpit\CockpitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CockpitApiController extends AbstractController
{
    #[Route('/api/ia/cockpit/snapshot', name: 'api_ia_cockpit_snapshot', methods: ['GET'])]
    public function snapshot(CockpitService $cockpitService): JsonResponse
    {
        $snapshot = $cockpitService->buildSnapshot();

        return $this->json($snapshot->toArray());
    }
}
