<?php

namespace App\Controller;

use App\Service\AI\CortexEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CortexController extends AbstractController
{
    #[Route('/cortex/test/{intent}', name: 'cortex_test', methods: ['GET'])]
    public function test(string $intent, CortexEngine $cortex): JsonResponse
    {
        $response = $cortex->process($intent);

        return new JsonResponse([
            'type' => $response->type,
            'message' => $response->message,
            'data' => $response->data,
        ]);
    }
}
