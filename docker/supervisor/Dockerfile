FROM php:8.1-fpm

# Install supervisord
RUN apt-get update && \
    apt-get install -y supervisor && \
    mkdir -p /var/log/supervisor

# Copy supervisord configuration
COPY supervisord.conf /etc/supervisord.conf

# Set entrypoint
ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
