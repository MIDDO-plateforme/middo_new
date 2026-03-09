<?php

namespace App\Service\File;

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

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
            $this->resizeAndOptimize($this->targetDirectory . '/' . $fileName);
        } catch (FileException $e) {
            throw new FileException('Erreur lors de l\'upload du fichier: ' . $e->getMessage());
        }

        return $fileName;
    }

    private function resizeAndOptimize(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            return;
        }

        $mimeType = $imageInfo['mime'];

        $sourceImage = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($filePath),
            'image/png'  => @imagecreatefrompng($filePath),
            'image/gif'  => @imagecreatefromgif($filePath),
            default      => null,
        };

        if (!$sourceImage) {
            return;
        }

        $targetWidth = 300;
        $targetHeight = 300;

        $currentWidth = imagesx($sourceImage);
        $currentHeight = imagesy($sourceImage);

        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

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

        match ($mimeType) {
            'image/jpeg' => imagejpeg($newImage, $filePath, 85),
            'image/png'  => imagepng($newImage, $filePath, 6),
            'image/gif'  => imagegif($newImage, $filePath),
        };

        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }

    public function delete(string $filename): bool
    {
        $filePath = $this->targetDirectory . '/' . $filename;

        return file_exists($filePath) ? unlink($filePath) : false;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
