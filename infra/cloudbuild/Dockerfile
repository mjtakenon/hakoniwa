FROM node:18.15.0-bullseye AS frontend_build_stage

WORKDIR /app

COPY ./app ./

RUN NODE_ENV=production yarn install && \
    yarn build


FROM php:8.2.4-fpm AS deploy_stage

ARG APP_ENV
ARG DB_PASSWORD
ARG GOOGLE_CLIENT_SECRET

WORKDIR /app

COPY ./infra/app/sources.list /etc/apt/sources.list

RUN \
  apt-get update && \
  apt-get install -y \
    gnupg \
    libzip-dev \
    cron \
    nginx \
    supervisor \
    sudo \
    git

RUN docker-php-ext-install \
  bcmath \
  pdo_mysql \
  mysqli \
  zip \
  opcache

RUN mkdir -p /var/run/php-fpm && \
    touch /var/run/php-fpm/php-fpm.sock && \
    chown www-data:www-data /var/run/php-fpm/php-fpm.sock && \
    chmod 660 /var/run/php-fpm/php-fpm.sock

COPY ./infra/app/nginx.conf /etc/nginx/nginx.conf
COPY ./infra/app/nginx-default.conf /etc/nginx/conf.d/
COPY ./infra/app/php-fpm.conf /usr/local/etc/php-fpm.d/php-fpm.conf
COPY ./infra/app/php.ini /usr/local/etc/php/php.ini
COPY ./infra/app/supervisor-app.conf /etc/supervisor/conf.d/

COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer
COPY --chown=www-data ./app ./
COPY --from=frontend_build_stage --chown=www-data /app/public/build /app/public/build

RUN composer install --optimize-autoloader --no-dev

RUN chmod -R ug+w /app/storage && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

CMD [ "/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf" ]
