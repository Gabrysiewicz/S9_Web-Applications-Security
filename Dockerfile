FROM php:8.2-apache

# Install mysqli and any other needed PHP extensions
RUN docker-php-ext-install mysqli

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Copy your application code to the container
COPY ./app /var/www/html

# Set the working directory
WORKDIR /var/www/html
