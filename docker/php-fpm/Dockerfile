FROM php:8.1.0-fpm-alpine

RUN apk add --no-cache curl git build-base zip libzip-dev libpq-dev oniguruma-dev libpng libjpeg-turbo-dev freetype-dev autoconf bash

RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

#Install xdebug
#RUN pecl install xdebug && docker-php-ext-enable xdebug
#COPY ./docker/php-fpm/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

#Install Redis
#RUN pecl install redis && docker-php-ext-enable redis

# Install GD
RUN docker-php-ext-configure gd --enable-gd --with-jpeg && \
        docker-php-ext-install gd

#Install pdo_*
RUN docker-php-ext-install pdo pdo_pgsql pgsql

ARG PUID=1000
ARG PGID=1000
RUN apk --no-cache add shadow && \
    groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

RUN set -x ;\
    ln -sf /usr/local/etc/php/php.ini /usr/local/etc/php/php.ini-development

COPY ./ /var/www
WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD php-fpm

EXPOSE 9000