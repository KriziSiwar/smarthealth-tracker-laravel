# Étape de construction
FROM node:20 AS node_build
WORKDIR /app
COPY smarthealth-tracker-laravel/package*.json ./
RUN npm install
COPY smarthealth-tracker-laravel/ .
RUN npm run build

# Étape de production
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier les fichiers du projet
COPY smarthealth-tracker-laravel/ /var/www
COPY --from=node_build /app/public/build /var/www/public/build

# Installer les dépendances PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Définir les permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache



    # Build stage
FROM node:20 AS node_build
WORKDIR /app
COPY smarthealth-tracker-laravel/package*.json ./
RUN npm install
COPY smarthealth-tracker-laravel/ .
RUN npm run build

# Production stage
FROM php:8.2-fpm

# ... rest of your Dockerfile ...

# Exposer le port 9000
EXPOSE 9000

# Démarrer PHP-FPM
CMD ["php-fpm"]
