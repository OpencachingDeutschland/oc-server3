#!/usr/bin/env bash

docker exec opencaching-php-fpm URL=__FRONTEND_URL__ htdocs/vendor/phpunit/phpunit/phpunit --stop-on-failure --stop-on-error --debug --verbose --coverage-clover=coverage.xml
