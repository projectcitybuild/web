#!/bin/bash

echo "=> Checking for running container..."

SERVICE_NAME="laravel.test"
CONTAINER_NAME=$(docker-compose ps -q "$SERVICE_NAME")

if [ "$(docker container inspect -f '{{.State.Status}}' "$CONTAINER_NAME")" == "running" ]; then
    echo "Running containers found"
else
    echo "Container not running. Booting it up..."
    sail up -d
fi

# ------------------------------------------------------------------------------------------------

echo "=> Running file watcher..."

./vendor/bin/sail npm run watch
