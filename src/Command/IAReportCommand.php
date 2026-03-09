<?php

namespace App\Command;

use App\IA\Health\IAHealthCheck;
use App\IA\Monitor\IAMonitor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:ia:report',
    description: 'Affiche l’état du système IA'
)]
final class IAReportCommand extends Command
{
    public function __construct(
        private readonly IAHealthCheck $health,
        private readonly IAMonitor $monitor
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("=== IA Health ===");
        foreach ($this->health->check() as $provider => $status) {
            $output->writeln("$provider : " . ($status ? "OK" : "FAIL"));
        }

        $output->writeln("\n=== IA Monitor ===");
        foreach ($this->monitor->all() as $entry) {
            $output->writeln(json_encode($entry, JSON_PRETTY_PRINT));
        }

        return Command::SUCCESS;
    }
}
