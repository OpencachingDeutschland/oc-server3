#!/usr/bin/env bash
#DESCRIPTION: executes okapi-update.php

php bin/okapi-update.php|grep -i -e current -e mutation
