FROM php:8.2-fpm

# Installation des dépendances système requises pour pdo_pgsql et composer
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP requises (MySQL et PostgreSQL)
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Intégration de Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copie des fichiers de l'application
COPY . /var/www/html

# Installation des dépendances Composer dans le conteneur
RUN composer install --no-interaction --optimize-autoloader


