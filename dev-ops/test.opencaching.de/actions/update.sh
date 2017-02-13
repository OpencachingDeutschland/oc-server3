#!/usr/bin/env bash

sh ./dev-ops/test.opencaching.de/actions/.check-git-status.sh

systemctl stop httpd.service

INCLUDE: .git-checkout.sh
INCLUDE: ./../../local.team-opencaching.de/actions/composer.sh
INCLUDE: ./../../local.team-opencaching.de/actions/db-update.sh
INCLUDE: ./../../local.team-opencaching.de/actions/import-stored-proc.sh
INCLUDE: ./../../local.team-opencaching.de/actions/import-sql-static.sh
INCLUDE: ./../../local.team-opencaching.de/actions/sf-migrations.sh
INCLUDE: ./../../local.team-opencaching.de/actions/import-translations.sh
INCLUDE: ./../../local.team-opencaching.de/actions/okapi-update.sh

systemctl start httpd.service
