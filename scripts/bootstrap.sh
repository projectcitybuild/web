#!/bin/bash

# Used only for first-time installation
if test -f "src/.env"; then
    echo ".env file already exists. Skipping copy..."
else
    cp src/.env.example src/.env
fi

docker-compose exec workspace composer install
docker-compose exec workspace npm install

echo "Bootstrap complete"