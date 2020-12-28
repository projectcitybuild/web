FROM php:7.4-fpm-alpine

ARG ENV

WORKDIR /var/www/html

RUN echo "UTC" > /etc/timezone

RUN apk add --no-cache --update zip unzip curl sqlite supervisor nodejs npm

RUN docker-php-ext-install mysqli pdo_mysql

RUN apk add shadow && \
    usermod -u 1000 www-data && \
    groupmod -g 1000 www-data

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

COPY ./docker/php-fpm/supervisord.conf /etc/supervisord.conf

RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/crontabs/root

COPY . .

# We don't need to bake-in dependencies since they'll be overwritten
# by a mounted volume in a dev environment
RUN if [ "$ENV" = "prod" ] ; then \
        composer install --no-dev ; \
        npm install ; \
        npm run prod ; \
    fi

RUN chown -R www-data:www-data . && \
    chmod 664 -R . && \
    chmod 775 -R ./storage

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
