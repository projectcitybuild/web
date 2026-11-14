#!/usr/bin/env bash

/usr/bin/mariadb --user=root --password="$MARIADB_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS testing;
    GRANT ALL PRIVILEGES ON \`testing\`.* TO '$MARIADB_USER'@'%';
EOSQL
