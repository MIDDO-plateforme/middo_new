FROM php:8.3-fpm

# --- Dépendances système ---
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip \
    nginx \
    supervisor \
    postgresql-client

# --- Extensions PHP ---
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl pdo pdo_pgsql zip opcache

# --- Composer ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- Code source ---
WORKDIR /var/www/html
COPY . .

# --- Création des dossiers nécessaires ---
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /var/www/html/var

# --- Installation des dépendances Symfony ---
RUN composer install --no-dev --optimize-autoloader --no-scripts

# --- Permissions ---
RUN chown -R www-data:www-data /var/www/html

# --- Configuration PHP-FPM ---
RUN sed -i 's|listen = .*|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf

# --- Configuration PHP personnalisée ---
COPY php-custom.ini /usr/local/etc/php/conf.d/php-custom.ini

# --- Configuration Nginx ---
COPY docker/nginx.conf /etc/nginx/sites-available/default

# --- Supervisor ---
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# --- Commande de démarrage ---
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

