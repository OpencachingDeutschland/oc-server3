#!/usr/bin/env bash

docker exec -i -u __USERKEY__ -t __PHP_FPM_ID__ ./php-cs-fixer fix --dry-run --using-cache=no
