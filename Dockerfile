# PHP 8.4 with Apache
FROM php:8.4-apache

# Install required extensions and tools
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        git \
        unzip \
        wget \
    ; \
    docker-php-ext-install mysqli pdo pdo_mysql; \
    rm -rf /var/lib/apt/lists/*

# Install and enable Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && { \
        echo "zend_extension=$(php -r 'echo ini_get("extension_dir");')/xdebug.so"; \
        echo "xdebug.mode=debug,develop,coverage"; \
        echo "xdebug.client_host=host.docker.internal"; \
        echo "xdebug.client_port=9003"; \
        echo "xdebug.start_with_request=yes"; \
      } > /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer --version

# Install PHPUnit globally via PHAR (optional; Composer-based PHPUnit also available at vendor/bin/phpunit)
RUN wget -O /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar \
    && chmod +x /usr/local/bin/phpunit \
    && phpunit --version

# Enable Apache rewrite
RUN a2enmod rewrite

# Configure DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Copy project (will be mounted by docker-compose during dev)
WORKDIR /var/www/html
