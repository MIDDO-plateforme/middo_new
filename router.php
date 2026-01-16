<?php

// router.php - Custom router for PHP built-in server with Symfony

// Si le fichier demandé existe physiquement, on le sert directement
if (is_file($_SERVER['DOCUMENT_ROOT'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false; // Servir le fichier statique
}

// Sinon, on route tout vers index.php (Symfony)
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require $_SERVER['DOCUMENT_ROOT'] . '/index.php';