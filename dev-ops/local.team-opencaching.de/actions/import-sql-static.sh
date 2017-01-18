#!/usr/bin/env bash

mysql -u __DB_USER__ -h__DB_HOST__ -p__DB_PASSWORD__ __DB_NAME__ < sql/static-data/data.sql
