FROM php:7.4-fpm-alpine

WORKDIR /var/www/html

RUN echo "UTC" > /etc/timezone

RUN apk add --no-cache --update zip unzip curl sqlite supervisor nodejs npm

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apk add shadow && \
    usermod -u 1000 www-data && \
    groupmod -g 1000 www-data

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

#RUN mkdir -p /etc/supervisor.d/
#COPY ./docker/php-fpm/supervisor.ini /etc/supervisor.d/supervisord.ini
##COPY ./docker/php-fpm/supervisord.conf /etc/supervisor.d/supervisord.conf
#
#RUN mkdir -p /run/php/
#RUN touch /run/php/php7.4-fpm.pid
#RUN touch /run/php/php7.4-fpm.sock
#
##COPY ./docker/php-fpm/php-fpm.conf /etc/php7/php-fpm.conf


COPY --chown=www-data:www-data . .
#RUN rm -rf ./docker

RUN composer install --no-dev

RUN npm install && npm run prod

RUN chmod 664 -R . && \
    chmod 775 -R ./storage

EXPOSE 80

#CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
