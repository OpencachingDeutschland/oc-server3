# This file is a "template" of what your parameters.yml file should look like
# Set parameters here that may be different on each deployment target of the app, e.g. development, staging, production.
# http://symfony.com/doc/current/best_practices/configuration.html#infrastructure-related-configuration
parameters:
    database_host:     __DB_HOST__
    database_port:     ~
    database_name:     __DB_NAME__
    database_user:     __DB_USER__
    database_password: __DB_PASSWORD__
# You should uncomment this if you want use pdo_sqlite
# database_path: "%kernel.root_dir%/data.db3"

    mailer_transport:  smtp
    mailer_host:       mailhog
    mailer_port:       1025
    mailer_user:       test
    mailer_password:   test
    mailer_auth_mode:  login

# A secret key that's used to generate certain security-related tokens
    secret:            ThisTokenIsNotSoSecretChangeIt
    api_secret:        ThisTokenIsNotSoSecretChangeIt
