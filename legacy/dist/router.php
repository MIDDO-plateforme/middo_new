<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedFile = __DIR__ . $uri;
if ($uri !== '/' && file_exists($requestedFile) && !is_dir($requestedFile)) {
    return false;
}
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
