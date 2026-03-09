<?php
require __DIR__.'/vendor/autoload.php';
$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();
$hasher = $container->get('security.user_password_hasher');
$user = new \App\Entity\User();
$hash = $hasher->hashPassword($user, 'Test1234!');
echo $hash;
?>