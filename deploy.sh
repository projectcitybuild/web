#!/bin/sh

cd src
php artisan down

cd ../
git reset --hard
git checkout master
git pull

cd src
composer install --no-interaction --optimize-autoloader --no-dev
npm install --production

npm run production

php artisan cache:clear
php artisan config:clear
php artisan config:cache
php artisan route:cache

# php artisan -v queue:restart

php artisan migrate --force

cd ../
chown -R www-data:www-data src
chmod -R 755 src

cd src
php artisan up