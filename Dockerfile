# PHP 8.4 with Apache
FROM php:8.4-apache

# Install required extensions
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        git \
        unzip \
    ; \
    docker-php-ext-install mysqli pdo pdo_mysql; \
    rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Configure DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Copy project (will be mounted by docker-compose during dev)
WORKDIR /var/www/html
