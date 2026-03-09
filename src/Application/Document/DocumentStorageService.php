<?php

namespace App\Application\Document;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DocumentStorageService
{
    private string $storagePath;

    public function __construct(string $projectDir)
    {
        $this->storagePath = $projectDir . '/var/user_documents';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    public function store(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($this->storagePath, $filename);
        return $filename;
    }

    public function getPath(string $filename): string
    {
        return $this->storagePath . '/' . $filename;
    }
}
