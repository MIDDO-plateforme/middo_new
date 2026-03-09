<?php

namespace App\KernelMemory;

class MemoryStore
{
    public function __construct(private string $storagePath)
    {
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    public function save(MemoryRecord $record): void
    {
        $file = $this->storagePath.'/memory_'.$record->timestamp.'.json';
        file_put_contents($file, json_encode($record->toArray(), JSON_PRETTY_PRINT));
    }

    public function all(): array
    {
        if (!is_dir($this->storagePath)) {
            return [];
        }

        $files = glob($this->storagePath.'/memory_*.json') ?: [];
        $records = [];

        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (!is_array($data)) {
                continue;
            }

            $records[] = new MemoryRecord(
                timestamp: $data['timestamp'] ?? 0,
                state: $data['state'] ?? []
            );
        }

        usort($records, fn($a, $b) => $a->timestamp <=> $b->timestamp);

        return $records;
    }
}
