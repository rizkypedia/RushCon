FROM php:7.4-fpm

ARG PHP_MEMORY_LIMIT=1G
ARG PROJECT_NAME=rushcon

ENV PHP_MEMORY_LIMIT=$PHP_MEMORY_LIMIT

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install development tools as well as additional packages and do a cleanup afterwards
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        ssh \
        zip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zlib1g-dev \
        libzip-dev \
        apt-utils \
        libicu-dev \
        g++ \
        libxml2-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-install soap \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip mbstring \
    && rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install opcache && docker-php-ext-enable opcache; \
    pecl install xdebug-2.7.2  && docker-php-ext-enable xdebug; \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    docker-php-source delete; \
    pecl clear-cache; \
    rm -rf \
        /usr/include/php \
        /usr/lib/php/build \
        /tmp/* \
        /root/.composer \
        /var/cache/apk/*;

COPY ./ /var/project
COPY ./setup/db_imports/ /dump
