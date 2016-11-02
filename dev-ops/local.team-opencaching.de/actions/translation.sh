#!/usr/bin/env bash
# get active translation from OC LIVE Host
ECHO "Retrieve and unpack translation"
cd /var/www/html/htdocs
curl -o translation.zip "https://www.opencaching.de/translations.zip"
unzip -o translation.zip