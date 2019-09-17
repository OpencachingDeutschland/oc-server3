#!/usr/bin/env bash

INCLUDE: ./composer.sh
# INCLUDE: ../../local.team-opencaching.de/actions/.init-database.sh

mysql -u__DB_USER__ -p__DB_PASSWORD__ __DB_NAME__ < sql/dump_v158.sql

# run database and cache updates
php bin/dbupdate.php

# Install OKAPI
curl __FRONTEND_URL__/okapi/update?install=true

# "updating database structures ..."
php bin/dbsv-update.php

INCLUDE: ../../local.team-opencaching.de/actions/import-stored-proc.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-sql-static.sh

# INCLUDE: ../../local.team-opencaching.de/actions/sf-migrations.sh
./htdocs/bin/console doctrine:migrations:migrate -n

INCLUDE: ../../local.team-opencaching.de/actions/okapi-update.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-translations.sh
