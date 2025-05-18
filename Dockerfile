# === Stage 1: Node.js for compiling assets ===
FROM node:18 AS node

WORKDIR /app

# Copy only the files needed for npm install
COPY package*.json vite.config.js ./
COPY resources ./resources
COPY public ./public

# Install dependencies and build
RUN npm install && npm run build

# === Stage 2: PHP with Apache ===
FROM php:8.2-apache

WORKDIR /var/www/html

# Install PHP dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# Enable mod_rewrite
RUN a2enmod rewrite

# Change Apache DocumentRoot to Laravel's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/c\<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' /etc/apache2/apache2.conf

# Copy Laravel project files
COPY . .

# Copy compiled assets from Node stage (for Vite)
COPY --from=node /app/public/build ./public/build

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Environment setup
RUN cp .env.example .env && php artisan key:generate

# Clear and cache Laravel config
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear \
    && php artisan config:cache && php artisan route:cache && php artisan view:cache

EXPOSE 80

CMD ["apache2-foreground"]
