FROM php:8.1-fpm

RUN pecl install redis && docker-php-ext-enable redis

WORKDIR /var/www/html