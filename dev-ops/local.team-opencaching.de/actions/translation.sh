#!/usr/bin/env bash
# get active translation from OC LIVE Host
echo "Retrieve and unpack translation"
cd /var/www/html/htdocs
curl -o translation.zip "http://www.opencaching.de/translations.zip"
unzip -o translation.zip
