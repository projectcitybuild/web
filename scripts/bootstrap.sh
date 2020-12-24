#!/bin/bash

echo "Checking for docker installation..."

docker help > /dev/null || \
    (echo "Error: Docker not found. Please manually install it first" && \
     exit 1)

# ------------------------------------------------------------------------------------------------

echo "Generating .env file..."

if [ -f ".env" ]; then
    echo "  .env file already exists. Skipping copy operation..."
else
    cp .env.example .env
    echo "  Generated new .env file from template. Please fill out the required details later"
fi

# ------------------------------------------------------------------------------------------------

echo "Checking for running containers..."

SERVICE_NAME="laravel.test"
CONTAINER_NAME=$(docker-compose ps -q "$SERVICE_NAME")

if [ "$(docker container inspect -f '{{.State.Status}}' "$CONTAINER_NAME")" == "running" ]; then
    echo "  Running containers found. Stoppping and removing..."
    docker-compose down
fi

# ------------------------------------------------------------------------------------------------

echo "Downloading composer dependencies..."

docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php74-composer:latest \
    composer install

# ------------------------------------------------------------------------------------------------

echo "Adding sail alias..."

if alias sail 2>/dev/null; then
    alias sail='bash vendor/bin/sail'
    echo "  Alias added"
else
    echo "  Alias already exists. Skipping..."
fi

# ------------------------------------------------------------------------------------------------

echo "Booting up container..."

./vendor/bin/sail up -d

# ------------------------------------------------------------------------------------------------

echo "Preparing database..."

./vendor/bin/sail artisan migrate --seed

# ------------------------------------------------------------------------------------------------

echo "Downloading NPM dependencies..."

./vendor/bin/sail npm install

# ------------------------------------------------------------------------------------------------

echo "Building front-end assets..."

./vendor/bin/sail npm run dev

# ------------------------------------------------------------------------------------------------

echo "Done! Ready for development"
