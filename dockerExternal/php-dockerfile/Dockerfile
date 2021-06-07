FROM php:7.4-apache
LABEL maintainer="dstecker@sploindy.de"

RUN apt-get update && apt-get upgrade -y && apt-get install -y libmcrypt-dev \
    libmagickwand-dev --no-install-recommends \
    zlib1g-dev libzip-dev unzip libxslt1-dev \
    && pecl install imagick \
    && pecl install xdebug \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install pdo_mysql zip gd bz2 \
    && docker-php-ext-install fileinfo gettext iconv xml \
    && docker-php-ext-install xmlrpc xmlwriter xsl \
    && docker-php-ext-install zip \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPStorm" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin --filename=composer && chmod +x /usr/bin/composer