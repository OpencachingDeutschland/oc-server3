#!/usr/bin/env bash
echo "Retrieve and unpack translation"
cd htdocs && curl -o translation.zip "http://www.opencaching.de/translations.zip" && unzip -o translation.zip
