#!/usr/bin/env bash

phpunit -c phpunit.xml.dist --stop-on-failure --stop-on-error --debug --verbose --coverage-html=build/artifacts/html-coverage
