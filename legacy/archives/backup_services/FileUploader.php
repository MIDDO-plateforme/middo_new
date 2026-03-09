<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private string $targetDirectory;
    private SluggerInterface $slugger;

    public function __construct(string $profilesDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $profilesDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Upload et traite une photo de profil
     * @return string Le nom du fichier uploadé
     */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            // Déplacer le fichier dans le dossier uploads/profiles
            $file->move($this->targetDirectory, $fileName);

            // Redimensionner et optimiser l'image
            $this->resizeAndOptimize($this->targetDirectory . '/' . $fileName);
        } catch (FileException $e) {
            throw new FileException('Erreur lors de l\'upload du fichier: ' . $e->getMessage());
        }

        return $fileName;
    }

    /**
     * Redimensionne et optimise une image (300x300 px)
     */
    private function resizeAndOptimize(string $filePath): void
    {
        // Vérifier que le fichier existe
        if (!file_exists($filePath)) {
            return;
        }

        // Détecter le type d'image
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            return; // Pas une image valide
        }

        $mimeType = $imageInfo['mime'];

        // Créer une ressource image selon le type
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = @imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $sourceImage = @imagecreatefrompng($filePath);
                break;
            case 'image/gif':
                $sourceImage = @imagecreatefromgif($filePath);
                break;
            default:
                return; // Type non supporté
        }

        if (!$sourceImage) {
            return; // Erreur création image
        }

        // Dimensions cibles (300x300 pour les photos de profil)
        $targetWidth = 300;
        $targetHeight = 300;

        // Dimensions actuelles
        $currentWidth = imagesx($sourceImage);
        $currentHeight = imagesy($sourceImage);

        // Créer une nouvelle image redimensionnée
        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Préserver la transparence pour PNG et GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        // Redimensionner avec haute qualité
        imagecopyresampled(
            $newImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $currentWidth,
            $currentHeight
        );

        // Sauvegarder l'image optimisée
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($newImage, $filePath, 85); // Qualité 85%
                break;
            case 'image/png':
                imagepng($newImage, $filePath, 6); // Compression niveau 6
                break;
            case 'image/gif':
                imagegif($newImage, $filePath);
                break;
        }

        // Libérer la mémoire
        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }

    /**
     * Supprime une photo de profil
     */
    public function delete(string $filename): bool
    {
        $filePath = $this->targetDirectory . '/' . $filename;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Retourne le répertoire cible
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
