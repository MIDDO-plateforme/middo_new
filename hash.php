<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

$factory = new PasswordHasherFactory([
    'common' => ['algorithm' => 'bcrypt'],
]);

$hasher = $factory->getPasswordHasher('common');

echo $hasher->hash('password') . PHP_EOL;
