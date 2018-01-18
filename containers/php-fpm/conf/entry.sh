#!/bin/sh

# start cron
/usr/sbin/crond -f -l 8

# use the php-fpm container's original entrypoint
docker-php-entrypoint php-fpm