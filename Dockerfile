# Use the Laravel Sail base image for PHP 8.3
FROM sail-8.3/app

# Install Supervisor
RUN apt-get update && apt-get install -y supervisor

# Copy Supervisor configuration file
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# CMD to start Supervisor
CMD ["/usr/bin/supervisord"]
