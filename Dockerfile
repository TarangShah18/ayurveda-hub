# Use official PHP image with Apache
FROM php:8.2-apache

# Enable mysqli (for MySQL)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy project files into Apache's web directory
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
