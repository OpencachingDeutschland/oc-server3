FROM webdevops/php-apache-dev:7.2

RUN apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install -y \
    default-mysql-client \
    nano

RUN composer self-update --1
