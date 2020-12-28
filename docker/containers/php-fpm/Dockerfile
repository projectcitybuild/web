FROM php:7.4-fpm-alpine

WORKDIR /var/www/html

RUN echo "UTC" > /etc/timezone

RUN apk add --no-cache zip unzip curl sqlite supervisor

#RUN docker-php-source extract \
#    && docker-php-source-delete

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

COPY . .
#COPY --chown=www-data:www-data . .
#RUN rm -rf ./docker

RUN composer install --no-dev

RUN chown -R www-data:www-data .

EXPOSE 80

#CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
