FROM php:8.3-fpm

# Update package list and install dependencies
RUN apt-get update && \
    apt-get install -y --no-install-recommends supervisor git && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
    mkdir -p /var/log/supervisor /etc/supervisor/conf.d

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisord.conf

# Set working directory
WORKDIR /var/www

# Copy composer.json and composer.lock
COPY composer.json composer.lock ./

# Install Composer v2
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --version=2.2.18

# Verify Composer installation
RUN composer --version

# Install PHP dependencies
RUN composer install 

# Copy the rest of the application
COPY . .

# Copy custom php.ini from host to container
COPY php.ini /usr/local/etc/php/php.ini

# Set entrypoint
ENTRYPOINT ["supervisord", "-c", "/etc/supervisord.conf"]

# Expose the port for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
