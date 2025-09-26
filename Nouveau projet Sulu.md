# Nouveau projet Sulu (2.6)

## 1. Créer le projet

```Bash
composer create-project sulu/skeleton mon-projet-sulu "2.6.*"
cd mon-projet-sulu
```



## 2. Configuration de Docker

Dans le compose.yaml
```YAML
services:
###> doctrine/doctrine-bundle ###
  database:
    image: mysql:5.7 # arm and x86/x64 compatible mysql image
    environment:
      MYSQL_DATABASE: ${MYSQL_DATABASE:-su_myapp}
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-ChangeMe} # You should definitely change the password in production
      MYSQL_ROOT_HOST: '%'
    volumes:
      - database_data:/var/lib/mysql
      # You may use a bind-mounted host directory instead, so that it is harder to accidentally remove the volume and lose all your data!
      # - ./docker/db/data:/var/lib/mysql:rw
###< doctrine/doctrine-bundle ###

volumes:
###> doctrine/doctrine-bundle ###
  database_data:
###< doctrine/doctrine-bundle ###
```

Dans le compose.override.yaml
```YAML
services:
  ###> symfony/mailer ###
  mailer:
    image: axllent/mailpit
    ports:
      - "1025"
      - "8025"
    environment:
      MP_SMTP_AUTH_ACCEPT_ANY: 1
      MP_SMTP_AUTH_ALLOW_INSECURE: 1
  ###< symfony/mailer ###

  ###> doctrine/doctrine-bundle ###
  database:
    ports:
      - "3306:3306"
  ###< doctrine/doctrine-bundle ###

  ###> php ###
  php:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
    volumes:
      - .:/var/www/html
      - cache_data:/var/www/html/var/cache
      - log_data:/var/www/html/var/log
    working_dir: /var/www/html
    depends_on:
      - database
  ###< php ###

  ###> nginx ###
  nginx:
    image: nginx:stable
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - php
  ###< nginx ###

volumes:
  cache_data:
  log_data:
```

Dans `docker/nginx/default.conf` _(le créer au besoin)_
```conf
server {
    listen 80;
    server_name localhost;

    root /var/www/html/public;

    index index.php index.html;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass php:9000;

        # Ajouts indispensables pour Symfony/Sulu
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT   $document_root;
        fastcgi_param PATH_INFO       $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_index index.php;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Dans `docker/php/Dockerfile` _(le créer au besoin)_
```Dockerfile
FROM php:8.2-fpm

# Installer dépendances système + extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo_mysql gd zip opcache \
    && docker-php-ext-enable opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html/var

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuration PHP personnalisée
COPY docker/php/conf.d/memory.ini /usr/local/etc/php/conf.d/memory.ini

WORKDIR /var/www/html
```

Dans `docker/php/conf.d/memory.ini` _(le créer au besoin)_
```ini
memory_limit = 1G
```



## 3. Configuration de l'administration

Dans `config/packages/sulu_admin.yaml`
```YAML
sulu_admin:
    email: "%env(SULU_ADMIN_EMAIL)%"
    forms:
        directories:
            - "%kernel.project_dir%/config/forms"
    lists:
        directories:
            - "%kernel.project_dir%/config/lists"
sulu_core: # Ajout des locales et traductions
    locales:
        fr: French
    translations:
        - fr
```



## 4. Variables d'environnement

Créer un `.env.local`
```conf
# Environnement
APP_ENV=dev
APP_SECRET='$ecretf0rt3st'
DEFAULT_LOCALE=fr

# Database
MYSQL_DATABASE=su_myapp
MYSQL_ROOT_PASSWORD=ChangeMe
DATABASE_URL="mysql://root:ChangeMe@database:3306/su_myapp?serverVersion=5.7&charset=utf8mb4"

# Sulu
SULU_ADMIN_EMAIL=my.mail@address.com
SULU_ADMIN_LOCALE=fr # default selected
```



## 5. Démarrer l'application

```Bash
docker compose up -d --build
```



## 6. Installation et configuration de l'app

```Bash
# Installation des dépendances
docker compose exec php composer install

# Installer les migrations si pas encore présentes
docker compose exec php composer require doctrine/doctrine-migrations-bundle

# Télécharger les traductions
docker compose exec php bin/console sulu:admin:download-language

# Base de données
docker compose exec php bin/console doctrine:database:create --if-not-exists
docker compose exec php bin/console doctrine:migrations:migrate -n
docker compose exec php bin/console sulu:build dev

# Utilisateur admin
docker compose exec php bin/console sulu:user:create

# Vider le cache
docker compose exec php bin/console cache:clear
```



## 7. Accès à l'app

* Frontend : http://localhost:8080
* Administration Sulu : http://localhost:8080/admin
* Mailpit (test emails) : http://localhost:8025
* Base de données : localhost:3306 (accessible avec un client MySQL)



## 8. Configurer le webspace

TODO
