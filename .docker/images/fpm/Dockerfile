FROM node:22-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.mjs tailwind.config.js postcss.config.js tsconfig.json ./
RUN npm run build

FROM php:8.4-fpm-alpine AS php-base

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev icu-dev libzip-dev libxml2-dev mariadb-connector-c-dev \
    && docker-php-ext-install bcmath curl dom intl pcntl pdo_mysql zip \
    && mkdir -p /usr/src/php/ext/redis \
    && curl -fsSL https://github.com/phpredis/phpredis/archive/6.3.0.tar.gz | tar xz -C /usr/src/php/ext/redis --strip 1 \
    && docker-php-ext-install redis \
    && apk add --no-cache icu-libs libzip mariadb-connector-c mariadb-client \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www/html

FROM php-base AS dev-fpm

WORKDIR /var/www/html

FROM php-base AS dependencies

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM php-base AS fpm

COPY --chown=www-data:www-data . .
COPY --from=dependencies --chown=www-data:www-data /var/www/html/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build
RUN composer dump-autoload --no-dev --optimize --no-interaction \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && ln -s ../storage/app/public public/storage

FROM nginx:1.27-alpine AS nginx

COPY .docker/images/nginx/nginx.conf /etc/nginx/templates/default.conf.template
COPY --from=fpm /var/www/html/public /var/www/html/public

FROM nginx:1.27-alpine AS dev-nginx

COPY .docker/images/nginx/nginx.conf /etc/nginx/templates/default.conf.template