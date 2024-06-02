FROM php:8.3-fpm

# Update package list
RUN apt-get update

# Install supervisor
RUN apt-get install -y --no-install-recommends supervisor

# Clean up APT when done
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Create necessary directories
RUN mkdir -p /var/log/supervisor /etc/supervisor/conf.d

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisord.conf

# Set working directory
WORKDIR /var/www

# Copy composer.json and composer.lock
COPY composer.json composer.lock ./

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --version=2.2.18

RUN composer update --lock
# Install PHP dependencies
RUN composer install

# Copy the rest of the application
COPY . .

# Set entrypoint
ENTRYPOINT ["supervisord", "-c", "/etc/supervisord.conf"]

# Expose the port for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
