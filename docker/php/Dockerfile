FROM php:8.2-fpm

# Installer dépendances système + extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo_mysql gd zip opcache \
    && docker-php-ext-enable opcache

RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html/var

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuration PHP personnalisée
COPY docker/php/conf.d/memory.ini /usr/local/etc/php/conf.d/memory.ini

WORKDIR /var/www/html
