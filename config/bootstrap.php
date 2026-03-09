<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php.cache')) {
    require dirname(__DIR__) . '/config/bootstrap.php.cache';
} elseif (class_exists(Dotenv::class)) {

    // 🔥 Correction : NE PAS charger .env en production
    $env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null;

    if ($env !== 'prod' && file_exists(dirname(__DIR__) . '/.env')) {
        (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
    }
}
