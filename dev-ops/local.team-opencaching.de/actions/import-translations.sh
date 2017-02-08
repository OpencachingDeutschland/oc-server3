#!/usr/bin/env bash

curl -o translation.zip "http://www.opencaching.de/translations.zip"
unzip -o htdocs/translation.zip

./htdocs/bin/console translation:update de --force
./htdocs/bin/console translation:update en --force
./htdocs/bin/console translation:update fr --force
