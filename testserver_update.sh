#!/usr/bin/env bash
#DESCRIPTION: update process for test.opencaching.de
CODE_ROOT="/var/www/gh-octest/code-root/code"

git fetch origin
git reset --hard origin/development
export COMPOSER_ALLOW_SUPERUSER=1

printf '\n***\n*** htdocs: composer install \n***\n'
cd ${CODE_ROOT}/htdocs && composer install --no-interaction --optimize-autoloader

printf '\n***\n*** htdocs_symfony: composer install \n***\n'
cd ${CODE_ROOT}/htdocs_symfony && composer install --no-interaction --optimize-autoloader


printf '\n***\n*** run database and cache updates \n***\n'
cd ${CODE_ROOT}
php bin/dbupdate.php

printf '\n***\n*** Install OKAPI \n***\n'
curl https://test.opencaching.de/okapi/update?install=true

printf '\n***\n*** updating database structures ... \n***\n'
php bin/dbsv-update.php
php bin/db-import-static.php
cd ${CODE_ROOT}/sql/stored-proc && php maintain.php

printf '\n***\n*** htdocs: doctrine migration \n***\n'
chmod 755 ${CODE_ROOT}/htdocs/bin/console
chmod -R 777 ${CODE_ROOT}/htdocs/var
${CODE_ROOT}/htdocs/bin/console doctrine:migrations:migrate -n
printf '\n***\nhtdocs_symfony: doctrine migration \n***\n'
cd ${CODE_ROOT}/htdocs_symfony/bin/console doctrine:migrations:migrate -n

printf '\n***\n*** update OKAPI \n***\n'
php bin/okapi-update.php|grep -i -e current -e mutation

printf '\n***\n*** htdocs: get translations.zip from oc.de \n***\n'
cd ${CODE_ROOT}/htdocs
curl -o translations.zip "https://www.opencaching.de/translations.zip"
unzip -o translations.zip
rm translations.zip

printf '\n***\n*** htdocs: process translations \n***\n'
cd ${CODE_ROOT}
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

systemctl reload php-fpm.service

printf '\n***\n*** htdocs_symfony: update caniuse list and yarns \n***\n'
cd ${CODE_ROOT}/htdocs_symfony
npx update-browserslist-db@latest -y
yarn install
yarn dev
