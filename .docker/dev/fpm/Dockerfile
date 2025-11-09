FROM php:8.3-fpm-alpine

# Install zip driver
RUN apk add --no-cache libzip-dev unzip && \
    docker-php-ext-install zip

# Install bcmath driver
RUN docker-php-ext-install bcmath

# Install MariaDB PDO driver
RUN apk add --no-cache mariadb-dev && \
    docker-php-ext-install pdo_mysql

# Install Redis extension
RUN mkdir -p /usr/src/php/ext/redis && \
    curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/6.0.2.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 && \
    docker-php-ext-install redis
