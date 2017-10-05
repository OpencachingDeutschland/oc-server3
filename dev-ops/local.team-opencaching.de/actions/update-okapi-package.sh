#!/usr/bin/env bash
#DESCRIPTION: update opencaching/okapi package

#cd htdocs && composer update opencaching/okapi

php -r "require_once __DIR__(sic!).'/dev-ops/local.team-opencaching.de/OkapiHelper.php'; OkapiHelper::updateOkapiMeta();"
