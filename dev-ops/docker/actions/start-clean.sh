#!/usr/bin/env bash
#DESCRIPTION: Reset docker containers and cached files to clear state

docker-compose rm --stop --force

if [ -d .idea ]; then git add --force .idea; fi
git clean -dfX
git reset HEAD .idea

docker-compose build --pull
docker-compose up --build -d

docker exec -u __USERKEY__ -t opencaching-webserver ./psh.phar docker:init
