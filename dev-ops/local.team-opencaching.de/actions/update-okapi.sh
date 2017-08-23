#!/usr/bin/env bash
#DESCRIPTION: updates the okapi to the latest version

wget -O okapi.tar.gz http://rygielski.net/r/okapi-latest
tar -xzf okapi.tar.gz -C htdocs
rm okapi.tar.gz
