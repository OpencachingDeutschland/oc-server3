#!/usr/bin/env bash
#DESCRIPTION: executes doctrine migrations

chmod 755 ./htdocs/bin/console
sudo chmod -R 777 ./htdocs/var
./htdocs/bin/console doctrine:migrations:migrate -n
