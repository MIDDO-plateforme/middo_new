<?php

namespace App\KernelWatcher;

class FileWatcher
{
    private array $lastState = [];

    public function __construct(private array $paths)
    {
        foreach ($paths as $path) {
            $this->lastState[$path] = $this->snapshot($path);
        }
    }

    public function detectChanges(): array
    {
        $events = [];

        foreach ($this->paths as $path) {
            $previous = $this->lastState[$path];
            $current = $this->snapshot($path);

            if ($previous !== $current) {
                $events[] = [
                    'path' => $path,
                    'type' => 'changed',
                    'timestamp' => time()
                ];
            }

            $this->lastState[$path] = $current;
        }

        return $events;
    }

    private function snapshot(string $path): string
    {
        if (!file_exists($path)) {
            return 'missing';
        }

        if (is_dir($path)) {
            return md5(json_encode(scandir($path)));
        }

        return md5_file($path) ?: 'error';
    }
}
