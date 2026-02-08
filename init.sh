#!/usr/bin/env bash
#DESCRIPTION: initialisation process to start developing

cd htdocs && composer install --optimize-autoloader

cd /var/www/html
cd htdocs_symfony && composer install --optimize-autoloader

cd /var/www/html
mysql -uroot -proot -hdb db < sql/dump_v158.sql

# run database and cache updates
php bin/dbupdate.php

# Install OKAPI
curl https://opencaching.ddev.site/okapi/update?install=true

# "updating database structures ..."
php bin/dbsv-update.php
php bin/db-import-static.php
cd sql/stored-proc && php maintain.php
cd /var/www/html

cat ./sql/static-data/*.sql | mysql -u root -hdb -proot db

chmod 755 ./htdocs/bin/console
chmod -R 777 ./htdocs/var
./htdocs/bin/console doctrine:migrations:migrate -n
./htdocs_symfony/bin/console doctrine:migrations:migrate -n


php bin/okapi-update.php|grep -i -e current -e mutation

cd htdocs
curl -o translations.zip "https://www.opencaching.de/translations.zip"
unzip -o translations.zip
rm translations.zip

cd /var/www/html

./htdocs/bin/console translation:update de --force
./htdocs/bin/console translation:update el --force
./htdocs/bin/console translation:update en --force
./htdocs/bin/console translation:update es --force
./htdocs/bin/console translation:update fr --force
./htdocs/bin/console translation:update it --force
./htdocs/bin/console translation:update nl --force
./htdocs/bin/console translation:update pl --force
./htdocs/bin/console translation:update ru --force
./htdocs/bin/console translation:import-legacy-translation

mysql -uroot -proot -hdb -e 'INSERT INTO user_roles SET user_id = 107469, role_id = 14' db
mysql -uroot -proot -hdb db < sql/user_content_sample.sql


cd htdocs_symfony
yarn install
yarn dev
