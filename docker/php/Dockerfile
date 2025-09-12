FROM php:8.2-fpm

# Installer dépendances système + extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo_mysql gd zip opcache

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
