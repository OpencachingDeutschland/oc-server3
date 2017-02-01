#!/usr/bin/env bash

chmod 755 ./htdocs/bin/console
sudo chmod -R 777 ./htdocs/var
./htdocs/bin/console doctrine:migrations:migrate -n
