FROM php:8.2.4-fpm

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
    iputils-ping net-tools \
    htop \
    vim \
    emacs \
    sudo

RUN docker-php-ext-install \
  bcmath \
  pdo_mysql \
  mysqli \
  zip \
  opcache

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug

RUN mkdir -p /var/run/php-fpm && \
    touch /var/run/php-fpm/php-fpm.sock && \
    chown www-data:www-data /var/run/php-fpm/php-fpm.sock && \
    chmod 660 /var/run/php-fpm/php-fpm.sock

ARG USERNAME=debian
ARG GROUPNAME=debian
ARG UID=1000
ARG GID=1000
ARG PASSWORD=debian
RUN groupadd -g $GID $GROUPNAME && \
    useradd -m -s /bin/bash -u $UID -g $GID -G sudo $USERNAME && \
    echo $USERNAME:$PASSWORD | chpasswd && \
    echo "$USERNAME ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers && \
    echo www-data:$PASSWORD | chpasswd && \
    echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

COPY ./infra/app/nginx.conf /etc/nginx/nginx.conf
COPY ./infra/app/nginx-default.conf /etc/nginx/conf.d/
COPY ./infra/app/php-fpm.conf /usr/local/etc/php-fpm.d/php-fpm.conf
COPY ./infra/app/php.ini /usr/local/etc/php/php.ini
COPY ./infra/app/supervisor-app.conf /etc/supervisor/conf.d/

CMD [ "/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf" ]
