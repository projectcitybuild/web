FROM php:8.3-cli-alpine

RUN addgroup -g 1000 -S user && adduser -u 1000 -D user -G user
RUN apk add --no-cache bash curl git nano
RUN apk add --no-cache composer

# Composer uses its php binary, but we want it to use the container's one
RUN rm -f /usr/bin/php83
RUN ln -s /usr/local/bin/php /usr/bin/php83

# Install zip driver
RUN apk add --no-cache libzip-dev unzip && \
    docker-php-ext-install zip

# Install bcmath driver
RUN docker-php-ext-install bcmath

# Install MariaDB PDO driver
RUN apk add --no-cache mariadb-connector-c-dev mariadb-client && \
    docker-php-ext-install pdo_mysql

# Install Redis driver
RUN mkdir -p /usr/src/php/ext/redis; \
    curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/6.0.2.tar.gz | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
    docker-php-ext-install redis

RUN apk add --no-cache nodejs npm

# Prevent container from exiting early
CMD ["sleep", "infinity"]
