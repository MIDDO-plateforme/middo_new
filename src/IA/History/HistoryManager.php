<?php

namespace App\IA\History;

class HistoryManager
{
    private string $file;

    public function __construct(string $projectDir)
    {
        $this->file = $projectDir . '/var/ia_history.json';

        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    public function add(array $entry): void
    {
        $history = $this->getAll();
        $entry['timestamp'] = date('Y-m-d H:i:s');
        $history[] = $entry;

        file_put_contents($this->file, json_encode($history, JSON_PRETTY_PRINT));
    }

    public function getAll(): array
    {
        return json_decode(file_get_contents($this->file), true) ?? [];
    }
}
