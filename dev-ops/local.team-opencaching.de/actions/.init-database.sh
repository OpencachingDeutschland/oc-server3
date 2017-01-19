#!/usr/bin/env bash

mysql -u__DB_USER__ -p__DB_PASSWORD__ __DB_NAME__ < sql/dump_v158.sql

# run database and cache updates
cd /var/www/html/ && php bin/dbupdate.php

# Install OKAPI
curl http://local.team-opencaching.de/okapi/update?install=true

# "updating database structures ..."
cd /var/www/html && php bin/dbsv-update.php
