<?php

namespace App\Command;

use App\Service\Notification\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:notifications:cleanup',
    description: 'Nettoie les notifications lues de plus de X jours',
)]
class CleanupNotificationsCommand extends Command
{
    public function __construct(
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'days',
            InputArgument::OPTIONAL,
            'Nombre de jours (défaut: 30)',
            30
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getArgument('days');

        $io->title('🧹 Nettoyage des notifications');
        $io->text("Suppression des notifications lues de plus de {$days} jours...");

        try {
            $count = $this->notificationService->cleanup($days);

            $io->success("✅ {$count} notification(s) supprimée(s) avec succès !");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('❌ Erreur lors du nettoyage: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
