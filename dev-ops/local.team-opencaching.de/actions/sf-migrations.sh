#!/usr/bin/env bash
#DESCRIPTION: executes doctrine migrations

chmod 755 ./htdocs/bin/console
sudo chmod -R 777 ./htdocs/var
./htdocs/bin/console doctrine:migrations:migrate -n

chmod 755 ./htdocs_symfony/bin/console
sudo chmod -R 777 ./htdocs_symfony/var
./htdocs_symfony/bin/console doctrine:migrations:migrate -n
