<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

$entityManager = $container->get('doctrine')->getManager();
$passwordHasher = $container->get(UserPasswordHasherInterface::class);

// Vérifie si l'utilisateur existe déjà
$userRepo = $entityManager->getRepository(User::class);
$existingUser = $userRepo->findOneBy(['email' => 'test@middo.app']);

if ($existingUser) {
    echo "✅ Utilisateur 'test@middo.app' existe déjà\n";
    echo "   Réinitialisation du mot de passe...\n";
    
    $hashedPassword = $passwordHasher->hashPassword($existingUser, 'Test1234!');
    $existingUser->setPassword($hashedPassword);
    $entityManager->flush();
    
    echo "✅ Mot de passe mis à jour : Test1234!\n";
} else {
    echo "Création d'un nouvel utilisateur...\n";
    
    $user = new User();
    $user->setEmail('test@middo.app');
    $user->setFirstName('Test');
    $user->setLastName('User');
    $user->setRoles(['ROLE_USER']);
    
    $hashedPassword = $passwordHasher->hashPassword($user, 'Test1234!');
    $user->setPassword($hashedPassword);
    
    $entityManager->persist($user);
    $entityManager->flush();
    
    echo "✅ Utilisateur créé :\n";
    echo "   Email: test@middo.app\n";
    echo "   Password: Test1234!\n";
}