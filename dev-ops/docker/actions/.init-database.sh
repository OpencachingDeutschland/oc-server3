#!/usr/bin/env bash

mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ -e "DROP DATABASE IF EXISTS __DB_NAME__"
mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ -e "CREATE DATABASE __DB_NAME__"
mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ __DB_NAME__ < sql/dump_v158.sql

# run database and cache updates
php bin/dbupdate.php

# Install OKAPI
curl __FRONTEND_URL__/okapi/update?install=true

# "updating database structures ..."
php bin/dbsv-update.php
