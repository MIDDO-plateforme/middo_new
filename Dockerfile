FROM php:8.3-fpm

RUN apt-get update && apt-get install -y git unzip libpq-dev libzip-dev libicu-dev zip nginx
RUN docker-php-ext-install intl pdo pdo_pgsql zip opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN php bin/console cache:clear --env=prod --no-debug
RUN php bin/console cache:warmup --env=prod

# FORCER CLEAR CACHE DOCTRINE METADATA
RUN rm -rf var/cache/prod/doctrine

# Config Nginx
RUN echo 'server { listen 80; root /var/www/html/public; location / { try_files $uri /index.php$is_args$args; } location ~ ^/index\.php(/|$) { fastcgi_pass 127.0.0.1:9000; fastcgi_split_path_info ^(.+\.php)(/.*)$; include fastcgi_params; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; } }' > /etc/nginx/sites-available/default

RUN chown -R www-data:www-data /var/www/html/var

CMD service nginx start && php-fpm
