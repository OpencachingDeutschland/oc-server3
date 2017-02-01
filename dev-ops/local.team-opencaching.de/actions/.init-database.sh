#!/usr/bin/env bash

mysql -u__DB_USER__ -p__DB_PASSWORD__ __DB_NAME__ < sql/dump_v158.sql

# run database and cache updates
sudo php bin/dbupdate.php

# Install OKAPI
curl __FRONTEND_URL__/okapi/update?install=true

# "updating database structures ..."
sudo php bin/dbsv-update.php
