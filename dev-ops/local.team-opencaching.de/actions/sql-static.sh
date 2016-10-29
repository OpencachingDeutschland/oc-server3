#!/usr/bin/env bash

   mysql -u root -hlocalhost -proot opencaching < sql/static-data/data.sql
