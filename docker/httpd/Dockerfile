FROM webdevops/php-apache-dev:7.4

# Prepare yarn to be installed
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

# Install yarn and some other tools
RUN apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install -y \
    default-mysql-client \
    nano \
    curl \
    yarn \
    gettext

# Install node.js
# Remove the GPG keyring file associated with the old repository, and old repository's list file, if existent
RUN rm -f /etc/apt/keyrings/nodesource.gpg \
    && rm -f /etc/apt/sources.list.d/nodesource.list
# Update local package index && install necessary packages for downloading and verifying new repository information
RUN apt-get update && apt-get install -y ca-certificates curl gnupg
# Create a directory for the new repository's keyring, if it doesn't exist
RUN mkdir -p /etc/apt/keyrings
# Download the new repository's GPG key and save it in the keyring directory
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
# Add the new repository's source list with its GPG key for package verification
# e.g. node_20.x defines the version to be installed
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
# Update local package index to recognize the new repository & Install Node.js from the new repository
RUN apt-get update && apt-get install -y nodejs

RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY vhost.conf /opt/docker/etc/httpd/vhost.conf

RUN composer self-update --2
