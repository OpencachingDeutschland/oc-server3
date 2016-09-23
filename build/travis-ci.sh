#!/usr/bin/env bash

function label {
    echo -e "\n\033[0;34m=> ${1}\033[0m\n"
}

function errorLabel {
    echo -e "\n\033[0;31m=> ${1}\033[0m\n"
}

label "Setup database"
mysql -u root -e 'DROP DATABASE IF EXISTS opencaching;'
mysql -u root -e 'CREATE DATABASE opencaching;'
mysql -u root -e "CREATE USER 'opencaching';"
mysql -u root -e "GRANT SELECT, INSERT, UPDATE, REFERENCES, DELETE, CREATE, DROP, ALTER, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EVENT ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u root -e "GRANT GRANT OPTION ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u root -e "SET PASSWORD FOR 'opencaching'@'%' = PASSWORD('opencaching');"


label "Configure Opencaching"
cp ./htdocs/config2/settings-sample-vagrant.inc.php ./htdocs/config2/settings.inc.php
cp ./htdocs/lib/settings-sample-vagrant.inc.php ./htdocs/lib/settings.inc.php
cp ./htdocs/statpics/htaccess-dist ./htdocs/statpics/.htaccess
cp ./htdocs/app/config/parameters_travis.yml ./htdocs/app/config/parameters.yml

label "import minimal dump to database"
mysql -uroot -proot opencaching < ./html/sql/dump_v158.sql

label "Run database and cache updates"
php ./bin/dbupdate.php

label "Install OKAPI"
curl http://localhost/okapi/update?install=true

label "updating database structures ..."

php ./bin/dbsv-update.php

if [ -f "./sql/stored-proc/maintain.php" ]; then
    label "reinstall triggers (new) ..."
    cd sql/stored-proc
    php maintain.php
    cd ../..
elif [ -f "./htdocs/doc/sql/stored-proc/maintain.php" ]; then
    label "-- reinstall triggers (old) ..."
    cd htdocs/doc/sql/stored-proc
    php maintain.php
    cd ../../../..
else
    label "error: maintain.php not found"
fi

if [ -f "./sql/static-data/data.sql" ]; then
  label "importing static data (new) ..."
  mysql -u root -hlocalhost -proot opencaching < ./sql/static-data/data.sql
elif [ -f "./htdocs/doc/sql/static-data/data.sql" ]; then
  echo "-- importing static data (old) ..."
  mysql -u root -hlocalhost -proot opencaching < ./htdocs/doc/sql/static-data/data.sql
else
  echo "error: data.sql not found"
  exit
fi

label "symfony migrations ..."
chmod 755 ./htdocs/bin/console
./htdocs/bin/console doctrine:migrations:migrate -n

echo "-- updating OKAPI database ..."
php bin/okapi-update.php|grep -i -e current -e mutation
