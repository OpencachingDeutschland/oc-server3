FROM phpdockerio/php56-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions, git and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php5-mysql php5-gd php5-pecl-http php5-imagick php5-odbc php5-xdebug php5-xmlrpc mysql-client unzip git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
