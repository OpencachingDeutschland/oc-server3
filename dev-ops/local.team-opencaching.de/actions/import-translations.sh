#!/usr/bin/env bash
#DESCRIPTION: import translation file from opencaching.de

cd htdocs && wget -O translations.zip "https://www.opencaching.de/translations.zip"
cd htdocs && unzip -o translations.zip
cd htdocs && rm translations.zip

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
