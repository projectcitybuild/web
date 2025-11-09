#!/bin/bash

set -e

composer install
php artisan key:generate
php artisan migrate --seed

npm install
npm run build
