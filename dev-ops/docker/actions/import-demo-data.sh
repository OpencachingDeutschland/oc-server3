#!/usr/bin/env bash
#DESCRIPTION: imports htdocs/opencaching_dump.sql for demo data

mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ -e 'DROP DATABASE IF EXISTS __DB_NAME__;'
mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ -e 'CREATE DATABASE __DB_NAME__;'
mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ -e "GRANT SELECT, INSERT, UPDATE, REFERENCES, DELETE, CREATE, DROP, ALTER, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EVENT ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ __DB_NAME__ < htdocs/opencaching_dump.sql

mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ __DB_NAME__ -e "UPDATE caches SET node = 4;"
mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ __DB_NAME__ -e "UPDATE cache_logs SET node = 4;"

INCLUDE: sf-migrations.sh
INCLUDE: ../../local.team-opencaching.de/actions/db-update.sh
INCLUDE: ../../local.team-opencaching.de/actions/import-sql-static.sh

