#!/usr/bin/env bash
#DESCRIPTION: initialisation process to start developing

INCLUDE: ./composer.sh
INCLUDE: ./.init-database.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-stored-proc.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-sql-static.sh
INCLUDE: ./sf-migrations.sh
INCLUDE: ../../local.team-opencaching.de/actions/okapi-update.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-translations.sh

I: cd /usr/local/bin && ln -sf /application/htdocs/vendor/phpunit/phpunit/phpunit && chmod 755 phpunit
