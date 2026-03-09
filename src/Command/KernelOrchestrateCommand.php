<?php

namespace App\Command;

use App\KernelValidator\KernelValidator;
use App\KernelCompiler\CompilerEngine;
use App\KernelOrchestrator\OrchestratorEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:kernel:orchestrate')]
class KernelOrchestrateCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $validator = new KernelValidator();
        $compiler = new CompilerEngine();
        $orchestrator = new OrchestratorEngine($validator, $compiler);

        $state = $orchestrator->orchestrate();

        $output->writeln("Orchestration terminée.");
        $output->writeln(json_encode($state->toArray(), JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
