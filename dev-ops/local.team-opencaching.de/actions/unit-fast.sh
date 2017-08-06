#!/usr/bin/env bash
#DESCRIPTION: executes unit tests

phpunit -c phpunit.xml.dist --stop-on-failure --stop-on-error --debug --verbose
