FROM php:8.2.4-fpm

WORKDIR /app

RUN \
  apt-get update && \
  apt-get install -y \
    git \
    libzip-dev \
    && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install zip

COPY --from=composer:2.3.7 /usr/bin/composer /usr/bin/composer

CMD [ "/bin/sh" ]

