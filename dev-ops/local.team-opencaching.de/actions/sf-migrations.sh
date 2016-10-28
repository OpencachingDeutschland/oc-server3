#!/usr/bin/env bash

chmod 755 ./htdocs/bin/console
./htdocs/bin/console doctrine:migrations:migrate -n
