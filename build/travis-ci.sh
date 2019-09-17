#!/usr/bin/env bash

function label {
    echo -e "\n\033[0;34m=> ${1}\033[0m\n"
}

function errorLabel {
    echo -e "\n\033[0;31m=> ${1}\033[0m\n"
}

label "setup database"
mysqladmin -u root password root
mysql -u root -proot -e 'DROP DATABASE IF EXISTS opencaching;'
mysql -u root -proot -e 'CREATE DATABASE opencaching;'
mysql -u root -proot -e "CREATE USER 'opencaching';"
mysql -u root -proot -e "GRANT SELECT, INSERT, UPDATE, REFERENCES, DELETE, CREATE, DROP, ALTER, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EVENT ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u root -proot -e "GRANT GRANT OPTION ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u root -proot -e "SET PASSWORD FOR 'opencaching'@'%' = PASSWORD('opencaching');"

chmod 777 ./psh.phar
