#!/usr/bin/env bash

php bin/okapi-update.php|grep -i -e current -e mutation
