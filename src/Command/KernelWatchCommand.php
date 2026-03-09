<?php

namespace App\Command;

use App\KernelWatcher\FileWatcher;
use App\KernelWatcher\WatchLoop;
use App\KernelValidator\KernelValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:kernel:watch')]
class KernelWatchCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $src = $this->projectDir . '/src';
        $config = $this->projectDir . '/config';

        $watcher = new FileWatcher([$src, $config]);
        $validator = new KernelValidator();

        $loop = new WatchLoop($watcher, $validator);

        $output->writeln("Watcher démarré…");

        $loop->run(function ($event, $result) use ($output) {
            $output->writeln("Changement détecté : {$event['path']}");

            if ($result->hasErrors()) {
                $output->writeln("❌ Erreurs détectées");
            } else {
                $output->writeln("✔️ OK");
            }
        });

        return Command::SUCCESS;
    }
}
