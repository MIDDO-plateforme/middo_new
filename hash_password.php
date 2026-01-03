<?php
require __DIR__.'/vendor/autoload.php';
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

$factory = new PasswordHasherFactory([
    'common' => ['algorithm' => 'auto']
]);
$hasher = $factory->getPasswordHasher('common');
$hash = $hasher->hash('Test1234!');
echo $hash;
?>