FROM php:7.4-fpm

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git libzip-dev zip

RUN docker-php-ext-install pdo_mysql zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && pecl clear-cache

WORKDIR /var/www/html/

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

COPY /project/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
