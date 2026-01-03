<?php

namespace App\Command;

use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create:notifications',
    description: 'Créer des notifications pour un utilisateur spécifique',
)]
class CreateNotificationsCommand extends Command
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

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::OPTIONAL, 'Email de l\'utilisateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userRepo = $this->em->getRepository(User::class);
        
        // Si email fourni, utiliser cet utilisateur
        $email = $input->getArgument('email');
        if ($email) {
            $user = $userRepo->findOneBy(['email' => $email]);
            if (!$user) {
                $io->error("Utilisateur avec email '$email' introuvable.");
                return Command::FAILURE;
            }
        } else {
            // Sinon, afficher la liste et demander
            $users = $userRepo->findAll();
            if (count($users) === 0) {
                $io->error('Aucun utilisateur trouvé.');
                return Command::FAILURE;
            }

            $io->title('Utilisateurs disponibles');
            foreach ($users as $u) {
                $io->writeln(sprintf('[%d] %s (%s %s)', 
                    $u->getId(), 
                    $u->getEmail(), 
                    $u->getFirstName(), 
                    $u->getLastName()
                ));
            }

            $userId = $io->ask('ID de l\'utilisateur pour créer les notifications', $users[0]->getId());
            $user = $userRepo->find($userId);
            
            if (!$user) {
                $io->error("Utilisateur ID $userId introuvable.");
                return Command::FAILURE;
            }
        }

        $sender = $user; // Utiliser le même utilisateur comme expéditeur pour les tests

        $io->title('Création de notifications de test');
        $io->text("Destinataire: {$user->getEmail()} (ID: {$user->getId()})");

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