# Use the official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Change Apache DocumentRoot to /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Set the <Directory> directive to match new DocumentRoot
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/c\<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' /etc/apache2/apache2.conf

# Copy Laravel project files into container
COPY . /var/www/html



# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer



# Set permissions for Laravel folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy default environment file
RUN cp .env.example .env

# Generate application key
RUN php artisan key:generate

# Clear any previously cached files
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Optimize Laravel
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache
# Install PHP dependencies for production
RUN composer install --no-dev --optimize-autoloader
# Expose Apache port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
