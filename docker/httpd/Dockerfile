FROM webdevops/php-apache-dev:7.2

RUN apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install -y \
    default-mysql-client \
    nano \
    curl \

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - && apt-get install -y nodejs npm
RUN npm install -g grunt-cli grunt
