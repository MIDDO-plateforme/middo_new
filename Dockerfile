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
    postgresql-client

# --- Extensions PHP ---
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl pdo pdo_pgsql zip opcache

# --- Composer ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- Code source ---
WORKDIR /var/www/html
COPY . .

# --- Création des dossiers nécessaires à Symfony ---
RUN mkdir -p var/cache var/log

# --- Installation des dépendances Symfony (prod) ---
RUN composer install --no-dev --optimize-autoloader --no-scripts

# --- Droits pour Symfony ---
RUN chown -R www-data:www-data var

# --- Configuration PHP-FPM pour écouter sur 9000 ---
RUN sed -i 's|listen = .*|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf

# --- Compilation du cache Symfony ---
RUN php bin/console cache:clear --env=prod --no-debug || true

# --- Configuration Nginx ---
RUN echo 'server { \
    listen 80; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
        try_files $uri /index.php$is_args$args; \
    } \
    location ~ \.php$ { \
        include fastcgi.conf; \
        fastcgi_pass 127.0.0.1:9000; \
    } \
}' > /etc/nginx/sites-available/default

# --- Lancement des services ---
CMD chown -R www-data:www-data /var/www/html/var && php-fpm -F & nginx -g "daemon off;"


