#!/usr/bin/env bash
#DESCRIPTION: executes unit tests

URL=http://docker.team-opencaching.de ./htdocs/vendor/bin/phpunit -c phpunit.xml.dist --stop-on-failure --stop-on-error --coverage-html=build/artifacts/html-coverage
