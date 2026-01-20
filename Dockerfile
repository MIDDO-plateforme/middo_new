FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload --optimize --classmap-authoritative

# CLEAR DOCTRINE METADATA
RUN php bin/console doctrine:cache:clear-metadata --env=prod || true
RUN php bin/console cache:clear --env=prod --no-debug
RUN php bin/console cache:warmup --env=prod

RUN chown -R www-data:www-data /var/www/html/var
RUN usermod -a -G 1000 www-data

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
