#!/usr/bin/env bash
#DESCRIPTION: Import translations from crowdin (see https://opencaching.atlassian.net/wiki/spaces/TRA/pages/5734402/Translation+Handling+Legacy#Import-translations-with-Crowdin-CLI-V3 for setup instructions)

# Avoid error "Downloaded translations don't match the current project configuration."
mkdir -p htdocs/app/Resources/translations/legacycode
touch htdocs/app/Resources/translations/legacycode/oc_legacy.csv

cd htdocs && crowdin download

docker exec -u __USERKEY__ -t __PHP_FPM_ID__ ./psh.phar docker:.translations-import
