<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DiagnosticController extends AbstractController
{
    #[Route('/api/diagnostic', methods: ['GET'])]
    public function diagnostic(): JsonResponse
    {
        $userFile = __DIR__ . '/../Entity/User.php';
        
        return $this->json([
            'user_file_exists' => file_exists($userFile),
            'user_file_path' => $userFile,
            'user_file_readable' => is_readable($userFile),
            'src_entity_dir' => scandir(__DIR__ . '/../Entity'),
            'doctrine_mappings' => $this->getParameter('kernel.project_dir') . '/config/packages/doctrine.yaml'
        ]);
    }
}
