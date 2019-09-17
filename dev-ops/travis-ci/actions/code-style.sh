#!/usr/bin/env bash

docker exec opencaching-php-fpm ./php-cs-fixer fix --dry-run --using-cache=no
