#!/usr/bin/env bash
#DESCRIPTION: download and extract translation file from opencaching.de

cd htdocs && curl -o translations.zip "https://www.opencaching.de/translations.zip"
cd htdocs && unzip -o translations.zip
cd htdocs && rm translations.zip
