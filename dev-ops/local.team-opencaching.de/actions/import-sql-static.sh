#!/usr/bin/env bash

cat ./sql/static-data/*.sql | mysql -u __DB_USER__ -h__DB_HOST__ -p__DB_PASSWORD__ __DB_NAME__
