#!/usr/bin/env bash

docker exec opencaching-php-fpm ./psh.phar travis-ci:code-style

docker exec opencaching-php-fpm ./psh.phar travis-ci:unit
