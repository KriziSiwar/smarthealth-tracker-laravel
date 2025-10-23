# Build stage for Node.js
FROM node:20 AS node_build
WORKDIR /app
COPY smarthealth-tracker-laravel/package*.json ./
RUN npm install
COPY smarthealth-tracker-laravel/ .
RUN npm run build

# Production stage
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY smarthealth-tracker-laravel/ /var/www

# Install PHP dependencies (no dev dependencies)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Copy built assets from node_build stage
COPY --from=node_build /app/public/build /var/www/public/build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Copy .env file
COPY smarthealth-tracker-laravel/.env.example /var/www/.env

# Install Prometheus PHP Client
RUN composer require promphp/prometheus_client_php

# Create storage for metrics
RUN mkdir -p /var/www/storage/framework/cache/metrics

# Copy supervisor configuration
RUN mkdir -p /var/log/supervisor
COPY docker/supervisor/conf.d/ /etc/supervisor/conf.d/

# Generate application key
RUN php artisan key:generate

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start services
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
