<?php
// public/router.php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si le fichier existe (assets, images, etc.), on le sert directement
$file = __DIR__ . $path;
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Sinon, on passe tout à Symfony (front controller)
require __DIR__ . '/index.php';
