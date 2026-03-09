<?php

namespace App\Command;

use App\KernelValidator\KernelValidator;
use App\KernelCompiler\CompilerEngine;
use App\KernelOrchestrator\OrchestratorEngine;
use App\KernelMemory\MemoryStore;
use App\KernelMemory\MemoryRecord;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:kernel:memory')]
class KernelMemoryCommand extends Command
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
        $arrayState = $state->toArray();

        $store = new MemoryStore($this->projectDir.'/var/kernel_memory');
        $record = new MemoryRecord(time(), $arrayState);
        $store->save($record);

        $output->writeln('État mémorisé.');
        $output->writeln(json_encode($arrayState, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
