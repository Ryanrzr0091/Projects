FROM php:8.2-apache

# Install PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable useful Apache modules
RUN a2enmod rewrite headers expires

# Drop in PHP settings
COPY php.ini /usr/local/etc/php/conf.d/php.ini