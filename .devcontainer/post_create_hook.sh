#!/bin/bash

set -e

composer install
php artisan key:generate
if [ ! -f storage/oauth-private.key ] || [ ! -f storage/oauth-public.key ]; then
	php artisan passport:keys --force
fi
php artisan migrate:fresh --seed

npm install
npm run build
