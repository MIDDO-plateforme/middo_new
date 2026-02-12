#!/usr/bin/env bash
set -o errexit

echo " Installation des dependances Composer..."
composer install --no-dev --optimize-autoloader

echo " Nettoyage du cache Symfony..."
php bin/console cache:clear --env=prod --no-debug

echo " Nettoyage du cache Doctrine..."
php bin/console doctrine:cache:clear-metadata --env=prod || true

echo " Warmup du cache..."
php bin/console cache:warmup --env=prod

echo " Build termine avec succes!"

chmod -R 777 var/cache var/log
