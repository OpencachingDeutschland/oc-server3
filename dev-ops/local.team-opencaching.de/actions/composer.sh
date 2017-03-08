#!/usr/bin/env bash

cd htdocs && sudo -u apache composer install --optimize-autoloader
