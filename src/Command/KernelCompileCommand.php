<?php

namespace App\Command;

use App\KernelValidator\KernelValidator;
use App\KernelCompiler\CompilerEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:kernel:compile')]
class KernelCompileCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $validator = new KernelValidator();
        $result = $validator->run();

        $compiler = new CompilerEngine();
        $compiled = $compiler->compile($result);

        $output->writeln("Compilation terminée.");
        $output->writeln(json_encode($compiled, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
