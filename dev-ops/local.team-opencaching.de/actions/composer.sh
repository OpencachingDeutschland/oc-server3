#!/usr/bin/env bash
#DESCRIPTION: composer install incl. --optimize-autoloader

cd htdocs && sudo -u apache composer install --optimize-autoloader
