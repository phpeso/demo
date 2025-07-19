FROM composer:latest AS build-composer

COPY composer.json composer.lock /app/
COPY src /app/src
WORKDIR /app/
RUN composer install --no-dev --classmap-authoritative --ignore-platform-req=ext-bcmath

FROM node:latest AS build-frontend

COPY package.json package-lock.json webpack.config.mjs /app/
COPY assets /app/assets
WORKDIR /app/
RUN npm install
RUN npm run webpack

FROM php:8.4-apache

RUN mkdir /var/www/var -m 1777
COPY --from=build-composer /app/vendor /var/www/vendor
COPY --from=build-frontend /app/html /var/www/html
COPY html/*.php /var/www/html
COPY src /var/www/src
