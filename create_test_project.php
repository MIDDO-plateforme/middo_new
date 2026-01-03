<?php

require __DIR__.'/vendor/autoload.php';

use App\Entity\Project;
use App\Entity\User;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();

$entityManager = $container->get('doctrine')->getManager();

// Trouver un utilisateur existant
$user = $entityManager->getRepository(User::class)->findOneBy([]);

if (!$user) {
    echo "❌ Aucun utilisateur trouvé. Créez un compte d'abord.\n";
    exit(1);
}

echo "✅ Utilisateur trouvé : " . $user->getEmail() . " (ID: " . $user->getId() . ")\n";

// Créer le projet
$project = new Project();
$project->setTitle('Projet Test API');
$project->setDescription('Projet de test pour valider les API de suggestions et matching IA');
$project->setStatus('active');
$project->setCreatedAt(new \DateTime());
$project->setOwner($user);

$entityManager->persist($project);
$entityManager->flush();

echo "✅ Projet créé avec succès !\n";
echo "📦 ID du projet : " . $project->getId() . "\n";
echo "📝 Titre : " . $project->getTitle() . "\n";
echo "👤 Propriétaire : " . $user->getEmail() . "\n";