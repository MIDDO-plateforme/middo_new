# Image de base PHP 8.3 avec Apache
FROM php:8.3-apache

# Installation des extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip gd mbstring xml

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration Apache
RUN a2enmod rewrite

# Copie du code MIDDO
WORKDIR /var/www/html
COPY . /var/www/html

# Installation des dépendances
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html/var

# Port exposé
EXPOSE 8000

# Démarrage
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
