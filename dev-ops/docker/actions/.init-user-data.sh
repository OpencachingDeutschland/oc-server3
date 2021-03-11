#!/usr/bin/env bash

mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ -e 'INSERT INTO user_roles SET user_id = 107469, role_id = 12' __DB_NAME__
mysql -u__DB_USER__ -p__DB_PASSWORD__ -h__DB_HOST__ __DB_NAME__ < sql/user_content_sample.sql
