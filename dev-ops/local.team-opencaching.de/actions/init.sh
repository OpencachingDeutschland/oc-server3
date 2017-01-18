#!/usr/bin/env bash

INCLUDE: ./composer.sh
INCLUDE: ./.init-database.sh
INCLUDE: ./import-sql-static.sh
INCLUDE: ./sf-migrations.sh
INCLUDE: ./okapi-update.sh
INCLUDE: ./import-translations.sh

cd /usr/local/bin && sudo ln -sf /var/www/html/htdocs/vendor/phpunit/phpunit/phpunit && sudo chmod 755 phpunit
