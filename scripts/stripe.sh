#!/bin/bash

if [ -z `docker ps -q --no-trunc | grep $(docker-compose ps -q stripe)` ]; then
  docker-compose up -d
fi

if [ $1 == 'payment' ]; then
    docker-compose exec stripe stripe trigger payment_intent.created
else
    echo "Unrecognized argument. Did you mean 'stripe payment'?"
fi

