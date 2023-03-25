FROM php:8.2.4-fpm

WORKDIR /app

RUN \
  apt-get update && \
  apt-get install -y \
    gnupg \
    libzip-dev \
    cron \
    nginx \
    iputils-ping net-tools

RUN docker-php-ext-install \
  bcmath \
  pdo_mysql \
  mysqli \
  zip \
  opcache

EXPOSE 80
