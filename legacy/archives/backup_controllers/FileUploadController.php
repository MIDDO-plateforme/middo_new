<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploadController extends AbstractController
{
    #[Route('/api/upload/document', name: 'api_upload_document', methods: ['POST'])]
    public function uploadDocument(Request $request): JsonResponse
    {
        return $this->handleUpload($request, 'documents', ['pdf', 'doc', 'docx', 'txt']);
    }

    #[Route('/api/upload/image', name: 'api_upload_image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        return $this->handleUpload($request, 'images', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    #[Route('/api/upload/avatar', name: 'api_upload_avatar', methods: ['POST'])]
    public function uploadAvatar(Request $request): JsonResponse
    {
        return $this->handleUpload($request, 'avatars', ['jpg', 'jpeg', 'png']);
    }

    #[Route('/api/storage/stats', name: 'api_storage_stats', methods: ['GET'])]
    public function getStorageStats(): JsonResponse
    {
        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        
        $stats = [
            'total_size' => $this->getDirectorySize($uploadsDir),
            'documents' => $this->getFolderStats($uploadsDir . '/documents'),
            'images' => $this->getFolderStats($uploadsDir . '/images'),
            'avatars' => $this->getFolderStats($uploadsDir . '/avatars'),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        return new JsonResponse([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function handleUpload(Request $request, string $folder, array $allowedExtensions): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Aucun fichier fourni'
            ], 400);
        }

        $extension = $file->guessExtension();
        if (!in_array($extension, $allowedExtensions)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Type de fichier non autorisé'
            ], 400);
        }

        $fileName = uniqid() . '.' . $extension;
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $folder;

        try {
            $file->move($uploadDir, $fileName);

            return new JsonResponse([
                'success' => true,
                'message' => 'Fichier uploadé avec succès',
                'file' => [
                    'name' => $fileName,
                    'path' => '/uploads/' . $folder . '/' . $fileName,
                    'size' => filesize($uploadDir . '/' . $fileName),
                    'type' => $extension
                ]
            ]);
        } catch (FileException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l\'upload : ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDirectorySize(string $dir): int
    {
        if (!is_dir($dir)) return 0;
        
        $size = 0;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    private function getFolderStats(string $dir): array
    {
        if (!is_dir($dir)) {
            return ['count' => 0, 'size' => 0];
        }

        $count = 0;
        $size = 0;
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $count++;
                $size += filesize($dir . '/' . $file);
            }
        }

        return ['count' => $count, 'size' => $size];
    }
}