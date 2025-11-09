#!/bin/bash

set -e

composer install
php artisan key:generate
php artisan migrate:fresh --seed

npm install
npm run build
