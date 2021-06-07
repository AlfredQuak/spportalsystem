FROM php:7.3-apache
LABEL maintainer="daniel.stecker@wolterskluwer.com"

RUN apt-get update && apt-get upgrade -y && apt-get install -y libmcrypt-dev \
    libmagickwand-dev --no-install-recommends \
    zlib1g-dev libzip-dev unzip libxslt1-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install pdo_mysql zip gd bz2 \
    && docker-php-ext-install fileinfo gettext iconv xml \
    && docker-php-ext-install xmlrpc xmlwriter xsl \
    && docker-php-ext-install zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/bin --filename=composer && chmod +x /usr/bin/composer