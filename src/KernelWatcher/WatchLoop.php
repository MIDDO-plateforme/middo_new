<?php

namespace App\KernelWatcher;

use App\KernelValidator\KernelValidator;

class WatchLoop
{
    public function __construct(
        private FileWatcher $watcher,
        private KernelValidator $validator
    ) {}

    public function run(callable $onEvent): void
    {
        while (true) {
            // Correction : utiliser $this->watcher
            $events = $this->watcher->detectChanges();

            foreach ($events as $event) {
                $onEvent($event, $this->validator->run());
            }

            usleep(300000); // 300ms
        }
    }
}
