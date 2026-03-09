<?php

namespace App\Controller\IA;

use App\IA\Vision\VisionPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VisionTestController extends AbstractController
{
    #[Route('/api/ia/vision/test', name: 'api_ia_vision_test', methods: ['GET'])]
    public function test(VisionPipeline $pipeline): JsonResponse
    {
        // Simulation d'une image encodée en base64
        $fakeImage = base64_encode('fake_image_data');

        $result = $pipeline->run($fakeImage);

        return $this->json([
            'status' => 'vision_test_ok',
            'result' => $result,
        ]);
    }
}
