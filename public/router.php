<?php

// router.php — Router pour le serveur PHP intégré (Render + Symfony)

// Récupère l’URL demandée
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Chemin du fichier demandé
$requestedFile = __DIR__ . $uri;

// Si le fichier demandé existe physiquement (image, CSS, JS, etc.)
// alors on le sert directement
if ($uri !== '/' && file_exists($requestedFile) && !is_dir($requestedFile)) {
    return false;
}

// Sinon, on route tout vers index.php (Symfony)
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__ . '/index.php';
