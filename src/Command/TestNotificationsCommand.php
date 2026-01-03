<?php
// bin/console app:test:notifications

namespace App\Command;

use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test:notifications',
    description: 'Créer des notifications de test',
)]
class TestNotificationsCommand extends Command
{
    private NotificationService $notificationService;
    private EntityManagerInterface $em;

    public function __construct(
        NotificationService $notificationService,
        EntityManagerInterface $em
    ) {
        parent::__construct();
        $this->notificationService = $notificationService;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userRepo = $this->em->getRepository(User::class);
        $users = $userRepo->findAll();

        if (count($users) < 1) {
            $io->error('Aucun utilisateur trouvé.');
            return Command::FAILURE;
        }

        $user = $users[0];
        $sender = count($users) > 1 ? $users[1] : $user;

        $io->title('Création de notifications de test');
        $io->text("Utilisateur: {$user->getEmail()}");

        $notif1 = $this->notificationService->notifyNewMessage($user, $sender, 'Bonjour ! Comment vas-tu ?');
        $io->writeln("✅ MESSAGE (ID: {$notif1->getId()})");

        $notif2 = $this->notificationService->notifyNewMatch($user, $sender);
        $io->writeln("✅ MATCH (ID: {$notif2->getId()})");

        $notif3 = $this->notificationService->notifyProjectInvitation($user, $sender, 'Projet MIDDO 2.0');
        $io->writeln("✅ PROJECT (ID: {$notif3->getId()})");

        $notif4 = $this->notificationService->create(
            recipient: $user,
            type: 'system',
            title: 'Mise à jour système',
            message: 'MIDDO a été mis à jour vers la version 2.3',
            actionUrl: '/changelog'
        );
        $io->writeln("✅ SYSTEM (ID: {$notif4->getId()})");

        $notif5 = $this->notificationService->create(
            recipient: $user,
            type: 'success',
            title: 'Profil complété !',
            message: 'Votre profil est maintenant complet à 100%',
            actionUrl: '/profile'
        );
        $io->writeln("✅ SUCCESS (ID: {$notif5->getId()})");

        $io->success('5 notifications créées !');
        $io->note('Rechargez http://localhost:8000 pour voir le badge (5)');

        return Command::SUCCESS;
    }
}