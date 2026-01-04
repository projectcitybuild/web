FROM php:8.4-fpm-alpine

# Install zip extension
RUN apk add --no-cache libzip-dev unzip && \
    docker-php-ext-install zip

# Install bcmath extension
RUN docker-php-ext-install bcmath

# Install intl extension
RUN apk add --no-cache icu-dev && \
    docker-php-ext-install intl

# Install MariaDB PDO driver
RUN apk add --no-cache mariadb-dev && \
    docker-php-ext-install pdo_mysql

# Install Redis extension
RUN mkdir -p /usr/src/php/ext/redis; \
    curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/6.3.0.tar.gz | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
    docker-php-ext-install redis
