#!/usr/bin/env bash
#DESCRIPTION: initialisation process to start developing

printf '\n***\n*** htdocs: composer install \n***\n'
cd htdocs && composer install --optimize-autoloader

printf '\n***\n*** htdocs_symfony: composer install \n***\n'
cd /var/www/html
cd htdocs_symfony && composer install --optimize-autoloader

printf '\n***\n*** put sql dump into database \n***\n'
cd /var/www/html
mysql -uroot -proot -hdb db < sql/dump_v158.sql

printf '\n***\n*** run database and cache updates \n***\n'
php bin/dbupdate.php

printf '\n***\n*** Install OKAPI \n***\n'
curl https://opencaching.ddev.site/okapi/update?install=true

printf '\n***\n*** updating database structures ... \n***\n'
php bin/dbsv-update.php
php bin/db-import-static.php
cd sql/stored-proc && php maintain.php
cd /var/www/html

printf '\n***\n*** put static sql data into database \n***\n'
cat ./sql/static-data/*.sql | mysql -u root -hdb -proot db

printf '\n***\n*** htdocs: doctrine migration \n***\n'
chmod 755 ./htdocs/bin/console
chmod -R 777 ./htdocs/var
./htdocs/bin/console doctrine:migrations:migrate -n
printf '\n***\nhtdocs_symfony: doctrine migration \n***\n'
./htdocs_symfony/bin/console doctrine:migrations:migrate -n

printf '\n***\n*** update OKAPI \n***\n'
php bin/okapi-update.php|grep -i -e current -e mutation

printf '\n***\n*** htdocs: get translations.zip from oc.de \n***\n'
cd htdocs
curl -o translations.zip "https://www.opencaching.de/translations.zip"
unzip -o translations.zip
rm translations.zip

printf '\n***\n*** htdocs: process translations \n***\n'
cd /var/www/html
./htdocs/bin/console translation:extract de --force
./htdocs/bin/console translation:extract el --force
./htdocs/bin/console translation:extract en --force
./htdocs/bin/console translation:extract es --force
./htdocs/bin/console translation:extract fr --force
./htdocs/bin/console translation:extract it --force
./htdocs/bin/console translation:extract nl --force
./htdocs/bin/console translation:extract pl --force
./htdocs/bin/console translation:extract ru --force
./htdocs/bin/console translation:import-legacy-translation

printf '\n***\n*** update root user in database \n***\n'
mysql -uroot -proot -hdb -e 'INSERT INTO user_roles SET user_id = 107469, role_id = 14' db
mysql -uroot -proot -hdb db < sql/user_content_sample.sql

printf '\n***\n*** htdocs_symfony: update caniuse list and yarns \n***\n'
cd htdocs_symfony
npx update-browserslist-db@latest -y
yarn install
yarn dev
