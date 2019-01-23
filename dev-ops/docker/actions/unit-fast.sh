#!/usr/bin/env bash
#DESCRIPTION: executes unit tests

URL=http://docker.team-opencaching.de phpunit -c phpunit.xml.dist --stop-on-failure --stop-on-error
