#!/usr/bin/env bash

docker exec -i -u __USERKEY__ -t __PHP_FPM_ID__ URL=__FRONTEND_URL__ htdocs/vendor/phpunit/phpunit/phpunit --stop-on-failure --stop-on-error --debug --verbose --coverage-clover=coverage.xml
