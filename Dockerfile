FROM php:8.3-fpm

# -------------------------------------------------------
# 1. Install system dependencies
# -------------------------------------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip \
    nginx \
    postgresql-client

# -------------------------------------------------------
# 2. Install PHP extensions (correct order for PHP 8.3)
# -------------------------------------------------------
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN docker-php-ext-install pdo pdo_pgsql zip opcache

# -------------------------------------------------------
# 3. Install Composer
# -------------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -------------------------------------------------------
# 4. Copy project
# -------------------------------------------------------
WORKDIR /var/www/html
COPY . .

# -------------------------------------------------------
# 5. Install PHP dependencies
# -------------------------------------------------------
RUN composer install --no-dev --optimize-autoloader

# -------------------------------------------------------
# 6. Clear Symfony cache
# -------------------------------------------------------
RUN php bin/console cache:clear --env=prod || true

# -------------------------------------------------------
# 7. Configure Nginx
# -------------------------------------------------------
RUN echo 'server { \
    listen 80; \
    root /var/www/html/public; \
    location / { try_files $uri /index.php$is_args$args; } \
    location ~ ^/index\.php(/|$) { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_split_path_info ^(.+\.php)(/.*)$; \
        include fastcgi_params; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    } \
}' > /etc/nginx/sites-available/default

# -------------------------------------------------------
# 8. Permissions
# -------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html/var

# -------------------------------------------------------
# 9. Start services
# -------------------------------------------------------
CMD service nginx start && php-fpm
