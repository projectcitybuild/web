FROM php:7.1-fpm-alpine

# install required packages, git and node+npm
RUN apk --no-cache add \
        git zip unzip nodejs freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
    && docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd mysqli pdo_mysql bcmath 

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install phpunit
RUN wget https://phar.phpunit.de/phpunit-6.4.phar && \
    chmod +x phpunit-6.4.phar && \
    mv phpunit-6.4.phar /usr/local/bin/phpunit

# set up cron
COPY containers/php-fpm/conf/entry.sh /entry.sh
ADD containers/php-fpm/conf/crontab.txt /tmp/crontab.txt
RUN chmod 755 /entry.sh
RUN /usr/bin/crontab /tmp/crontab.txt


WORKDIR /var/www/html

# pull in dependency definitions so we can reuse this layer when no
# dependency changes occured
COPY src/composer.json src/composer.lock ./
RUN composer install --no-scripts --no-autoloader
COPY src/package.json src/package-lock.json ./
# RUN npm install

# bake our source code into the image last so that code changes don't
# flush the previous layers
COPY src .
RUN chown -R www-data:www-data /var/www/html

# minify our production css and javascript
# RUN npm run production

# run post-install scripts since we disabled them during the composer install 
# phase (files weren't baked-in yet)
# RUN composer dump-autoload --optimize && \
	# composer run-script post-install-cmd

ENTRYPOINT ["sh", "/entry.sh"]