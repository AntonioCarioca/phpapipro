FROM php:8.4-fpm-alpine

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
  && apk add --no-cache \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libintl \
    oniguruma-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) opcache pdo pdo_mysql intl gd zip bcmath mbstring pcntl \
  && apk del -f .build-deps

COPY --from=composer:2.8.10 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_HOME=/tmp/composer

WORKDIR /phpapipro

EXPOSE 9000