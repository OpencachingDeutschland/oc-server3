FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions, git and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.2-phpdbg php7.2-mysql php7.2-gd php-imagick php7.2-odbc php-xdebug php7.2-xmlrpc mysql-client unzip git gettext \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
