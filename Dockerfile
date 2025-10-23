FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy only necessary files first (optimize build cache)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Install Node.js dependencies and build assets
RUN if [ -f "package.json" ]; then \
    npm install --no-audit --prefer-offline && \
    npm run build; \
    fi

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Generate application key if not exists
RUN if [ ! -f ".env" ]; then \
    cp .env.example .env && \
    php artisan key:generate; \
    fi

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
