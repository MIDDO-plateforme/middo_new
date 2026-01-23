FROM php:8.3-fpm

ENV CACHE_BUST=2026-01-20-19-30

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip

# Install PHP extensions
RUN docker-php-ext-install intl pdo pdo_pgsql zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN composer dump-autoload --optimize --classmap-authoritative

# Clear and warmup Symfony cache
RUN php bin/console cache:clear --env=prod --no-debug || true
RUN php bin/console cache:warmup --env=prod || true

# Permissions
RUN chown -R www-data:www-data /var/www/html/var

EXPOSE 8000

CMD ["php-fpm"]
