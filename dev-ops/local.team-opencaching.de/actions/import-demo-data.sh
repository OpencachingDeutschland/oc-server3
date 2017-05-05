#!/usr/bin/env bash

mysql -u root -proot -e 'DROP DATABASE IF EXISTS __DB_NAME__;'
mysql -u root -proot -e 'CREATE DATABASE __DB_NAME__;'
mysql -u root -proot -e "GRANT SELECT, INSERT, UPDATE, REFERENCES, DELETE, CREATE, DROP, ALTER, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EVENT ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u root -proot __DB_NAME__ < htdocs/opencaching_dump.sql

mysql -u root -proot __DB_NAME__ -e "UPDATE caches SET node = 4;"
mysql -u root -proot __DB_NAME__ -e "UPDATE cache_logs SET node = 4;"

INCLUDE: sf-migrations.sh
INCLUDE: db-update.sh
INCLUDE: import-sql-static.sh

