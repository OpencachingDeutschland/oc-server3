#!/usr/bin/env bash
#DESCRIPTION: executes doctrine migrations

chmod 755 ./htdocs/bin/console
chmod -R 777 ./htdocs/var
./htdocs/bin/console doctrine:migrations:migrate -n
./htdocs_symfony/bin/console doctrine:migrations:migrate -n
