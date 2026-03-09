<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiTestController extends AbstractController
{
    #[Route('/api/storage/stats', name: 'api_storage_stats', methods: ['GET'])]
    public function storageStats(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'storage' => [
                'total_capacity' => 10737418240,
                'used_space' => 2147483648,
                'available_space' => 8589934592,
                'usage_percentage' => 20
            ],
            'files_count' => [
                'total' => 156,
                'documents' => 89,
                'images' => 52,
                'others' => 15
            ],
            'largest_files' => [
                [
                    'file_name' => 'video_presentation.mp4',
                    'file_size' => 524288000
                ],
                [
                    'file_name' => 'database_backup.sql',
                    'file_size' => 314572800
                ]
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}