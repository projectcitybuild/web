#!/bin/bash

if [ -z `docker ps -q --no-trunc | grep $(docker-compose ps -q stripe)` ]; then
  docker-compose up -d
fi

docker-compose exec stripe stripe trigger payment_intent.created
