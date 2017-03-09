#!/usr/bin/env bash

URL=__FRONTEND_URL__ htdocs/vendor/phpunit/phpunit/phpunit --stop-on-failure --stop-on-error --debug --verbose # --coverage-html=build/artifacts/html-coverage
