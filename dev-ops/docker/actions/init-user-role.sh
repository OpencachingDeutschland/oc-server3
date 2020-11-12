#!/usr/bin/env bash
#DESCRIPTION: Init symfony frontend

mysql -u __DB_USER__ -p __DB_PASSWORD__ -h __DB_HOST__ -e 'INSERT INTO user_roles SET user_id = 107469, role_id = 12' database-name
