FROM php:8.6-cli-alpine

RUN addgroup -g 1000 -S user && adduser -u 1000 -D user -G user
RUN apk add --no-cache bash curl git nano

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install zip extension
RUN apk add --no-cache libzip-dev unzip && \
    docker-php-ext-install zip

# Install bcmath extension
RUN docker-php-ext-install bcmath

# Install intl extension
RUN apk add --no-cache icu-dev && \
    docker-php-ext-install intl

# Install MariaDB PDO driver
RUN apk add --no-cache mariadb-connector-c-dev mariadb-client && \
    docker-php-ext-install pdo pdo_mysql

# Install xdebug and sqlite for CI pipelines
# PHP 8.6's DOM HTML5 parser uses Lexbor headers from the PHP source tree.
RUN apk add --no-cache --virtual .ci-build-deps $PHPIZE_DEPS curl-dev libxml2-dev oniguruma-dev sqlite-dev linux-headers && \
    CPPFLAGS="$PHP_CPPFLAGS -I/usr/src/php -I/usr/src/php/ext/lexbor" docker-php-ext-install curl dom mbstring pcntl pdo_sqlite && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    apk add --no-cache libxml2 oniguruma sqlite-libs && \
    apk del .ci-build-deps

# Install Redis driver
RUN mkdir -p /usr/src/php/ext/redis; \
    curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/6.3.0.tar.gz | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
    docker-php-ext-install redis

RUN apk add --no-cache nodejs npm

# Prevent container from exiting early
USER user
CMD ["sleep", "infinity"]
