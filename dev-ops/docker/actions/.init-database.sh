#!/usr/bin/env bash

# run database and cache updates
php bin/dbupdate.php

# Install OKAPI
curl __FRONTEND_URL__/okapi/update?install=true

# "updating database structures ..."
php bin/dbsv-update.php
