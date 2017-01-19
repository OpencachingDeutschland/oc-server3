#!/usr/bin/env bash

URL=http://127.0.0.1 phpunit -c phpunit.xml.dist --stop-on-failure --stop-on-error --debug --verbose --coverage-html=build/artifacts/html-coverage
