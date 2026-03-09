<?php

namespace App\Command;

use App\KernelValidator\KernelValidator;
use App\KernelValidator\Scanners\NamespaceScanner;
use App\KernelValidator\Scanners\DoctrineEntityScanner;
use App\KernelValidator\Scanners\FileStructureScanner;
use App\KernelValidator\Scanners\ServiceScanner;
use App\KernelValidator\Reports\ReportFormatter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:kernel:validate')]
class KernelValidateCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $src = $this->projectDir . '/src';
        $services = $this->projectDir . '/config/services.yaml';

        $validator = new KernelValidator();
        $validator->addScanner(new NamespaceScanner($src));
        $validator->addScanner(new DoctrineEntityScanner($src));
        $validator->addScanner(new FileStructureScanner($src));
        $validator->addScanner(new ServiceScanner($src, $services));

        $result = $validator->run();

        $formatter = new ReportFormatter();
        $output->writeln($formatter->format($result));

        return $result->hasErrors() ? Command::FAILURE : Command::SUCCESS;
    }
}
