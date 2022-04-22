#!/bin/bash

function error () {
    echo -e "\033[31m ERROR \033[0m $1"
    exit 1
}

function check_port () {
    if lsof -Pi:$1 -sTCP:LISTEN -t > /dev/null; then
        error "$2 port ($1) is already in use"
    fi
}

echo "=> Checking for docker installation..."

docker help > /dev/null || \
    (error "Docker not found. Please manually install it first")

# ------------------------------------------------------------------------------------------------

echo "=> Checking for .env file..."

if [ ! -f ".env" ]; then
    error ".env file not found. Please run 'cp .env.example .env' first"
fi

# ------------------------------------------------------------------------------------------------

echo "=> Checking for running containers..."

# TODO: extract service name from docker-compose.yml file
SERVICE_NAME="laravel.test"
CONTAINER_NAME=$(docker-compose ps -q "$SERVICE_NAME")

if [ "$(docker container inspect -f '{{.State.Status}}' "$CONTAINER_NAME")" == "running" ]; then
    echo "Running containers found. Stopping and removing..."
    docker-compose down
fi

# ------------------------------------------------------------------------------------------------

echo "=> Checking port availability..."

check_port 3000 "Browsersync"
check_port 3306 "MariaDB"
check_port 6379 "Redis"
check_port 8025 "Mailhog"
check_port 9000 "MinIO"

# ------------------------------------------------------------------------------------------------

echo "=> Downloading composer dependencies..."

if ! docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs; then
        error "Error: Failed to download and install composer dependencies"
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
    error "Error: Failed to start container"
fi

# ------------------------------------------------------------------------------------------------

echo "=> Generating app key..."

./vendor/bin/sail artisan key:generate || exit 1

# ------------------------------------------------------------------------------------------------

echo "=> Preparing database..."

./vendor/bin/sail artisan migrate:fresh --seed || exit 1

# ------------------------------------------------------------------------------------------------

echo "=> Downloading NPM dependencies..."

./vendor/bin/sail npm install || exit 1

# ------------------------------------------------------------------------------------------------

echo "=> Building front-end assets..."

./vendor/bin/sail npm run dev || exit 1

# ------------------------------------------------------------------------------------------------

echo "Done! Ready for development"
