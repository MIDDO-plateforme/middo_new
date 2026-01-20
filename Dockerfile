# Image PHP 8.3 CLI (pas Apache)
FROM php:8.3-cli

# Extensions PHP + PostgreSQL
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip gd mbstring xml opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Code
WORKDIR /var/www/html
COPY . /var/www/html

# Install dependencies + FIX DOCTRINE AUTOLOAD
RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# FIX DOCTRINE: clear metadata cache prod
RUN php bin/console doctrine:cache:clear-metadata --env=prod || true
RUN php bin/console cache:clear --env=prod --no-debug
RUN php bin/console cache:warmup --env=prod

# Permissions + groupe 1000 Render secrets
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public
RUN groupadd -g 1000 secrets || true
RUN usermod -a -G 1000 www-data

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
