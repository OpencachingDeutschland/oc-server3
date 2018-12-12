#!/usr/bin/env bash
#DESCRIPTION: initialisation process to start developing

INCLUDE: ./composer.sh
INCLUDE: ./.init-database.sh
# INCLUDE: ./import-stored-proc.sh
# INCLUDE: ./import-sql-static.sh
# INCLUDE: ./sf-migrations.sh
# INCLUDE: ./okapi-update.sh
# INCLUDE: ./import-translations.sh
#
# I: cd /usr/local/bin && ln -sf /application/htdocs/vendor/phpunit/phpunit/phpunit && sudo chmod 755 phpunit
