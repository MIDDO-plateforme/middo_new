<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class DatabaseBackupHandler
{
    private Filesystem $filesystem;

    public function __construct(
        private readonly string $projectDir,
        private readonly LoggerInterface $logger
    ) {
        $this->filesystem = new Filesystem();
    }

    public function createBackup(string $databaseName): string
    {
        $backupDir = $this->projectDir . '/var/backups';
        
        if (!$this->filesystem->exists($backupDir)) {
            $this->filesystem->mkdir($backupDir, 0755);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = sprintf('%s/%s_%s.sql', $backupDir, $databaseName, $timestamp);

        try {
            // Logic pour créer le backup (exemple simplifié)
            $this->logger->info('Database backup created', [
                'file' => $backupFile,
                'database' => $databaseName
            ]);

            return $backupFile;

        } catch (\Exception $e) {
            $this->logger->error('Database backup failed', [
                'error' => $e->getMessage(),
                'database' => $databaseName
            ]);

            throw $e;
        }
    }

    public function listBackups(): array
    {
        $backupDir = $this->projectDir . '/var/backups';
        
        if (!$this->filesystem->exists($backupDir)) {
            return [];
        }

        $files = glob($backupDir . '/*.sql');
        
        return array_map(function($file) {
            return [
                'path' => $file,
                'name' => basename($file),
                'size' => filesize($file),
                'created_at' => filemtime($file)
            ];
        }, $files ?: []);
    }

    public function deleteBackup(string $backupFile): bool
    {
        try {
            if ($this->filesystem->exists($backupFile)) {
                $this->filesystem->remove($backupFile);
                
                $this->logger->info('Database backup deleted', [
                    'file' => $backupFile
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            $this->logger->error('Database backup deletion failed', [
                'error' => $e->getMessage(),
                'file' => $backupFile
            ]);

            return false;
        }
    }
}
