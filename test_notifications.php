<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');
$notificationService = $container->get('App\Service\NotificationService');

$userRepo = $em->getRepository('App\Entity\User');
$users = $userRepo->findAll();

if (count($users) < 1) {
    echo "❌ Aucun utilisateur trouvé.\n";
    exit(1);
}

$user = $users[0];
$sender = count($users) > 1 ? $users[1] : $user;

echo "🎯 Utilisateur: {$user->getEmail()}\n";
echo "📧 Création de 5 notifications...\n\n";

$notif1 = $notificationService->notifyNewMessage($user, $sender, 'Bonjour ! Comment vas-tu ?');
echo "✅ MESSAGE (ID: {$notif1->getId()})\n";

$notif2 = $notificationService->notifyNewMatch($user, $sender);
echo "✅ MATCH (ID: {$notif2->getId()})\n";

$notif3 = $notificationService->notifyProjectInvitation($user, $sender, 'Projet MIDDO 2.0');
echo "✅ PROJECT (ID: {$notif3->getId()})\n";

$notif4 = $notificationService->create(
    recipient: $user,
    type: 'system',
    title: 'Mise à jour système',
    message: 'MIDDO a été mis à jour vers la version 2.3',
    actionUrl: '/changelog'
);
echo "✅ SYSTEM (ID: {$notif4->getId()})\n";

$notif5 = $notificationService->create(
    recipient: $user,
    type: 'success',
    title: 'Profil complété !',
    message: 'Votre profil est maintenant complet à 100%',
    actionUrl: '/profile'
);
echo "✅ SUCCESS (ID: {$notif5->getId()})\n";

echo "\n🎉 5 notifications créées !\n";
echo "🔄 Rechargez http://localhost:8000\n";
echo "🔔 Vous devriez voir un badge rouge avec \"5\"\n";