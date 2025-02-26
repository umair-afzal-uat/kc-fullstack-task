FROM php:8.3-apache

# Install MySQL PDO extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite (if needed)
RUN a2enmod rewrite

CMD ["apache2-foreground"]
