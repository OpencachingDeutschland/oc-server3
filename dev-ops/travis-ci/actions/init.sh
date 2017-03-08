#!/usr/bin/env bash

INCLUDE: ./composer.sh
INCLUDE: ../../local.team-opencaching.de/actions/.init-database.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-stored-proc.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-sql-static.sh
INCLUDE: ../../local.team-opencaching.de/actions/sf-migrations.sh
INCLUDE: ../../local.team-opencaching.de/actions/okapi-update.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-translations.sh

I: cd /usr/local/bin && sudo ln -sf /var/www/html/htdocs/vendor/phpunit/phpunit/phpunit && sudo chmod 755 phpunit

