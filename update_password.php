<?php
require __DIR__.'/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$params = [
    'dbname' => 'middo_db',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

$conn = DriverManager::getConnection($params);

$sql = "UPDATE user SET password = :password, roles = :roles WHERE email = :email";
$conn->executeStatement($sql, [
    'password' => '$2y$13$MkPa4yc9MNS.HgM0hb.110kbJBNi1hdJL2AxrkFUDtJ.kvjddzW',
    'roles' => '["ROLE_USER"]',
    'email' => 'mbaudouin61@gmail.com'
]);

echo "Password updated successfully!\n";
