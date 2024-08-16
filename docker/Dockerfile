# Use the official PHP image with PHP-FPM
ARG COMPOSER_VERSION=2.5.8
ARG PHP_VERSION=8.3

FROM composer:${COMPOSER_VERSION} AS composer
FROM php:${PHP_VERSION}-fpm AS php

ENV COMPOSER_HOME=/var/composer

# Install system dependencies and PostgreSQL development headers
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zlib1g-dev \
    git \
    unzip \
    libzip-dev \
    libpq-dev \ 
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install intl xml zip pdo pdo_pgsql

# Copy custom php.ini configuration
COPY php.ini /usr/local/etc/php/

# Set the working directory
WORKDIR /var/www/html

# Copy the application code
COPY . .

# Install Composer

ENV XDEBUG_MODE="coverage, debug"
### INSTALL COMPOSER ###
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/html/config/jwt \
    && chown -R www-data:www-data /var/www/html/config/jwt

# Expose port 9000 for PHP-FPM
EXPOSE 9000
