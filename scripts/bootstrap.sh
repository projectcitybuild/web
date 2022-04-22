#!/bin/bash

echo "=> Checking for docker installation..."

docker help > /dev/null || \
    (echo "Error: Docker not found. Please manually install it first" && \
     exit 1)

# ------------------------------------------------------------------------------------------------

echo "=> Checking for .env file..."

if [ ! -f ".env" ]; then
    (echo ".env file not found. Please run 'cp .env.example .env' first" && \
     exit 1)
fi

# ------------------------------------------------------------------------------------------------

echo "=> Checking for running containers..."

SERVICE_NAME="laravel.test"
CONTAINER_NAME=$(docker-compose ps -q "$SERVICE_NAME")

if [ "$(docker container inspect -f '{{.State.Status}}' "$CONTAINER_NAME")" == "running" ]; then
    echo "Running containers found. Stoppping and removing..."
    docker-compose down
fi

# ------------------------------------------------------------------------------------------------

echo "=> Downloading composer dependencies..."

if ! docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs; then
        echo "Error: Failed to download and install composer dependencies"
        exit 1
fi

# ------------------------------------------------------------------------------------------------

#echo "=> Adding sail alias..."
#
## Allow aliases created in here to be accessed by the outside
#shopt -s expand_aliases
#
#unalias sail 2>/dev/null
#
#alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
#

# ------------------------------------------------------------------------------------------------

echo "=> Booting up container..."

if ! ./vendor/bin/sail up -d; then
    echo "Error: Failed to start container"
    exit 1
fi

# ------------------------------------------------------------------------------------------------

echo "=> Generating app key..."

./vendor/bin/sail artisan key:generate


# ------------------------------------------------------------------------------------------------

echo "=> Preparing database..."

./vendor/bin/sail artisan migrate:fresh --seed

# ------------------------------------------------------------------------------------------------

echo "=> Downloading NPM dependencies..."

./vendor/bin/sail npm install

# ------------------------------------------------------------------------------------------------

echo "=> Building front-end assets..."

./vendor/bin/sail npm run dev

# ------------------------------------------------------------------------------------------------

echo "Done! Ready for development"
