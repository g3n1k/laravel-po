FROM php:8.3-fpm-alpine

RUN addgroup -g 1000 laravel \
    && adduser -G laravel -g laravel -s /bin/sh -D laravel

RUN apk add --no-cache \
    git \
    unzip \
    autoconf \
    g++ \
    make \
    libpq-dev \
    icu-dev \
    && docker-php-ext-install pdo pdo_pgsql intl

# Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
USER laravel
