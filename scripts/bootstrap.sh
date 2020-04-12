#!/bin/bash

# Used only for first-time installation
if test -f "src/.env"; then
    echo ".env file already exists. Skipping copy..."
else
    cp src/.env.example src/.env
fi

echo "Bootstrap complete"