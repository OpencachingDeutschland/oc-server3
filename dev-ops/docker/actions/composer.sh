#!/usr/bin/env bash
#DESCRIPTION: composer install incl. --optimize-autoloader

cd htdocs && composer install --ignore-platform-reqs --optimize-autoloader
cd htdocs_symfony && composer install --ignore-platform-reqs --optimize-autoloader
