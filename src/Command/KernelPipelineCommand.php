<?php

namespace App\Command;

use App\KernelPipelines\AdminHelperPipeline;
use App\KernelPipelines\PipelineEngine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:pipeline:admin')]
class KernelPipelineCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pipeline = AdminHelperPipeline::build();
        $engine = new PipelineEngine();

        $result = $engine->execute($pipeline, [
            'text' => 'Attestation CAF du 12/03/2024 pour le dossier 12345.'
        ]);

        $output->writeln(json_encode($result, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
