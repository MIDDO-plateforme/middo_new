FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip \
    nginx \
    postgresql-client

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl pdo pdo_pgsql zip opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

RUN php bin/console cache:clear --env=prod --no-debug || true

RUN php bin/console cache:clear --env=prod || true

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

    RUN mkdir -p /var/www/html/var
    RUN chown -R www-data:www-data /var/www/html/var

CMD php-fpm -F & nginx -g "daemon off;"