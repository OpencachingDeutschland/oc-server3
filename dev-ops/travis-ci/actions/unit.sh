#!/usr/bin/env bash
#DESCRIPTION: executes unit tests with code coverage

URL=__FRONTEND_URL__ ./htdocs/vendor/bin/phpunit -c phpunit.xml.dist --stop-on-failure --stop-on-error --coverage-html=build/artifacts/html-coverage
