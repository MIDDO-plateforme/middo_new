#!/usr/bin/env bash
set -o errexit

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Clearing Symfony cache..."
php bin/console cache:clear --env=prod --no-debug

echo "Clearing Doctrine metadata cache..."
php bin/console doctrine:cache:clear-metadata --env=prod || true

echo "Warming up cache..."
php bin/console cache:warmup --env=prod

echo "Build complete!"