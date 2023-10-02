FROM php:8.2-fpm-alpine AS base
RUN apk update \
    && apk upgrade \
    && apk add --no-cache linux-headers php-pear php-openssl bash zip libzip-dev freetype-dev libjpeg-turbo-dev libpng \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip \
    && docker-php-ext-install mysqli

FROM base AS development
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY conf.d/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" 
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-3.2.2 \
    && docker-php-ext-enable xdebug \
    && pecl clear-cache

FROM base AS production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"


FROM development AS final