FROM php:8.1-fpm

# Install necessary packages and supervisord
RUN apt-get update && \
    apt-get install -y supervisor && \
    mkdir -p /var/log/supervisor /etc/supervisor/conf.d

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisord.conf

# Set working directory
WORKDIR /var/www

# Copy composer.json and composer.lock
COPY composer.json composer.lock ./

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install

# Copy the rest of the application
COPY . .

# Set entrypoint
ENTRYPOINT ["supervisord", "-c", "/etc/supervisord.conf"]

# Expose the port for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
