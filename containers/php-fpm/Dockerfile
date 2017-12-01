FROM php:7.1-fpm-alpine

# install required packages, git and node+npm
RUN apk --no-cache add \
    git zip unzip nodejs \
    && docker-php-ext-install mysqli pdo_mysql bcmath

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install phpunit
RUN wget https://phar.phpunit.de/phpunit-6.4.phar && \
    chmod +x phpunit-6.4.phar && \
    mv phpunit-6.4.phar /usr/local/bin/phpunit


WORKDIR /var/www/html

# pull in dependency definitions so we can cache this step if
# there haven't been any dependency changes
COPY src/composer.json src/composer.lock ./
RUN composer install --no-scripts --no-autoloader
COPY src/package.json .
RUN npm install

# bake our source code into the image last so that code changes don't
# flush the dependency caches
COPY src .
RUN chown -R www-data:www-data .

# minify our production css and javascript
RUN npm run production

# run post-install scripts since we disabled them during the composer install 
# phase (files weren't baked-in yet)
RUN composer dump-autoload --optimize && \
	composer run-script post-install-cmd