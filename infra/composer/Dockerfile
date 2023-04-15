FROM php:8.2.4-fpm

WORKDIR /app

RUN \
  apt-get update && \
  apt-get install -y \
    git \
    libzip-dev \
    sudo \
    && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install zip

COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer

ARG USERNAME=debian
ARG GROUPNAME=debian
ARG UID=1000
ARG GID=1000
ARG PASSWORD=debian
RUN groupadd -g $GID $GROUPNAME && \
    useradd -m -s /bin/bash -u $UID -g $GID -G sudo $USERNAME && \
    echo $USERNAME:$PASSWORD | chpasswd && \
    echo "$USERNAME ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

CMD [ "/bin/sh" ]

