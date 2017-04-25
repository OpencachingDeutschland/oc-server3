#!/usr/bin/env bash

cd htdocs && curl -o translation.zip "http://www.opencaching.de/translations.zip"
cd htdocs && unzip -o translation.zip
cd htdocs && rm translation.zip

./htdocs/bin/console translation:update de --force
./htdocs/bin/console translation:update el --force
./htdocs/bin/console translation:update en --force
./htdocs/bin/console translation:update es --force
./htdocs/bin/console translation:update fr --force
./htdocs/bin/console translation:update it --force
./htdocs/bin/console translation:update nl --force
./htdocs/bin/console translation:update pl --force
./htdocs/bin/console translation:update ru --force
