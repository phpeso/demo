FROM composer:latest AS build-composer

COPY composer.json composer.lock /app/
WORKDIR /app/
RUN composer install --no-dev --classmap-authoritative

FROM php:8.4-apache

RUN mkdir /var/www/var -m 1777
COPY --from=build-composer /app/vendor /var/www/vendor
COPY html /var/www/html
COPY src /var/www/src
