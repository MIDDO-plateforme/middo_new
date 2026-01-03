<?php

namespace App\Message;

class DatabaseBackupMessage
{
    private string $backupType;
    private array $options;

    public function __construct(string $backupType = 'full', array $options = [])
    {
        $this->backupType = $backupType;
        $this->options = $options;
    }

    public function getBackupType(): string
    {
        return $this->backupType;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}