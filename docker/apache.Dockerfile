ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-apache

ENV TZ=America/New_York
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y \
    libcurl4-gnutls-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    gd \
    mysqli \
    pdo_mysql \
    zip \
    intl

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN sed -i 's/Listen 80/Listen 8000/' /etc/apache2/ports.conf
RUN sed -i 's/^\tOptions Indexes FollowSymLinks/\tOptions FollowSymLinks/' /etc/apache2/apache2.conf
RUN a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY auto-config.inc.php /var/www/html/auto-config.inc.php

RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/memory-limit.ini
RUN ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
    ln -sf /proc/self/fd/1 /var/log/apache2/error.log

EXPOSE 8000
