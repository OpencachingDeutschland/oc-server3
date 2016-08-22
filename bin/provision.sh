#!/usr/bin/env bash

# enter url for SQL dump. e.g. http://opencaching.de/dump.sql
DUMP_URL=""

function label {
    echo -e "\n\033[0;34m=> ${1}\033[0m\n"
}

function errorLabel {
    echo -e "\n\033[0;31m=> ${1}\033[0m\n"
}

if [[ -z "$DUMP_URL" ]]; then
    errorLabel "No dump url given.\nPlease edit provision.sh and enter an url for the sql dump\nRun 'vagrant provision' if you are done.\n\n"
    exit 1
fi

label "Install required components"
yum -y install mariadb-server mariadb
systemctl start mariadb.service
systemctl enable mariadb.service
yum -y install httpd
systemctl start httpd.service
systemctl enable httpd.service
firewall-cmd --permanent --zone=public --add-service=http
firewall-cmd --permanent --zone=public --add-service=https
firewall-cmd --reload
yum -y install php epel-release php-devel ImageMagick-devel ImageMagick gcc
yum -y install php-gd php-odbc php-pear php-xml php-xmlrpc php-mbstring
yum -y install php-snmp php-soap curl curl-devel php-mysql php-pdo php-pecl-zip
yum -y install vim vim-common mutt mlocate man-pages zip mod_ssl patch
yum -y install gcc-c++ ruby ruby-devel

label "Install crowdin-cli"
gem install crowdin-cli

label "Adjust Apache and MariaDB configuration"
# set max_allowed_packet
cat <<EOF > /etc/my.cnf.d/server.cnf
[server]

[mysqld]
max_allowed_packet = 1024M
init-connect='SET NAMES utf8mb4'
collation_server=utf8mb4_general_ci
character_set_server=utf8mb4
character-set-client-handshake = FALSE

[embedded]

[mysqld-5.5]

[mariadb]

[mariadb-5.5]

[mysql]
default-character-set=utf8mb4

[client]
default-character-set=utf8mb4
EOF

cat <<EOF > /etc/httpd/conf/httpd.conf
ServerRoot "/etc/httpd"

Listen 80

Include conf.modules.d/*.conf

User apache
Group apache

ServerAdmin root@localhost

<Directory />
    AllowOverride none
    Require all denied
</Directory>

DocumentRoot "/var/www/html/htdocs"

<Directory "/var/www">
    AllowOverride None
    # Allow open access:
    Require all granted
</Directory>

<Directory "/var/www/html/htdocs">
    Options Indexes FollowSymLinks
    AllowOverride None
    Require all granted

    ErrorDocument 404 /404.php

    RewriteEngine On
    RewriteRule ^((OC|GC)[A-Za-z0-9]{1,5})$ /searchplugin.php?userinput=\$1 [NC,L]

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^(.*)$ /symfony_app.php [QSA,L]
</Directory>

<Directory "/var/www/html/htdocs/statpics">
    AllowOverride All
</Directory>

<Directory "/var/www/html/htdocs/okapi">
    AllowOverride All
</Directory>

<IfModule dir_module>
    DirectoryIndex index.html
</IfModule>

<Files ".ht*">
    Require all denied
</Files>

ErrorLog "logs/error_log"

LogLevel warn

<IfModule log_config_module>
    LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combined
    LogFormat "%h %l %u %t \"%r\" %>s %b" common

    <IfModule logio_module>
      # You need to enable mod_logio.c to use %I and %O
      LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" %I %O" combinedio
    </IfModule>

    CustomLog "logs/access_log" combined
</IfModule>

<IfModule alias_module>
    ScriptAlias /cgi-bin/ "/var/www/cgi-bin/"
</IfModule>

<Directory "/var/www/cgi-bin">
    AllowOverride None
    Options None
    Require all granted
</Directory>

<IfModule mime_module>
    TypesConfig /etc/mime.types

    AddType application/x-compress .Z
    AddType application/x-gzip .gz .tgz

    AddType text/html .shtml
    AddOutputFilter INCLUDES .shtml
</IfModule>

AddDefaultCharset UTF-8

<IfModule mime_magic_module>
    MIMEMagicFile conf/magic
</IfModule>

IncludeOptional conf.d/*.conf
EOF

label "upgrade to php 5.6"
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm

yum -y install yum-plugin-replace
yum -y replace php-common --replace-with=php56w-common
yum -y install phpmyadmin

cat <<EOF > /etc/httpd/conf.d/phpMyAdmin.conf
Alias /phpMyAdmin /usr/share/phpMyAdmin
Alias /phpmyadmin /usr/share/phpMyAdmin

<Directory /usr/share/phpMyAdmin/>
   AddDefaultCharset UTF-8

   <IfModule mod_authz_core.c>
     # Apache 2.4
     Require all granted
   </IfModule>
   <IfModule !mod_authz_core.c>
     # Apache 2.2
     Order Allow,deny
     Allow from all
   </IfModule>
</Directory>
EOF

systemctl restart mariadb
systemctl restart httpd

cat /etc/sysconfig/selinux | sed -e 's/SELINUX=permissive/SELINUX=disabled/' > /etc/sysconfig/selinux.tmp
mv /etc/sysconfig/selinux.tmp /etc/sysconfig/selinux


label "Adjust php.ini"
cat /etc/php.ini | sed -e 's/upload_max_filesize = 2M/upload_max_filesize = 10M/' > /etc/php.ini.tmp
echo "extension=imagick.so" >> /etc/php.ini.tmp
mv /etc/php.ini.tmp /etc/php.ini


label "Install imagick"
printf "\n" | pecl install imagick


label "Setup database user"
mysqladmin -u root password root
mysql -u root -proot -e "CREATE USER 'opencaching';"
mysql -u root -proot -e "GRANT SELECT, INSERT, UPDATE, REFERENCES, DELETE, CREATE, DROP, ALTER, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EVENT ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u root -proot -e "GRANT GRANT OPTION ON \`opencaching\`.* TO 'opencaching'@'%';"
mysql -u root -proot -e "SET PASSWORD FOR 'opencaching'@'%' = PASSWORD('opencaching');"


label "Configure Opencaching"
cd /var/www/html
cp ./htdocs/config2/settings-sample-vagrant.inc.php ./htdocs/config2/settings.inc.php
cp ./htdocs/lib/settings-sample-vagrant.inc.php ./htdocs/lib/settings.inc.php
cp ./htdocs/statpics/htaccess-dist ./htdocs/statpics/.htaccess

cp /var/www/html/local/prodsys/phpzip.php /var/www/html/bin/
sed -i 's/\/path\/to\/htdocs\/download\/zip\//\/var\/www\/html\/htdocs\/download\/zip\//' /var/www/html/bin/phpzip.php

label "Install Composer"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/bin/composer
chmod 0777 /usr/bin/composer


label "Composer install"
cd /var/www/html/htdocs && composer install

label "Download translations from Crowdin"
cd /var/www/html/htdocs && crowdin-cli --identity=.crowdin.yaml download

label "Install Database Dump from '$DUMP_URL'"
label "Download SQL Dump"
curl -o opencaching_dump.sql.gz "$DUMP_URL"
gzip -d opencaching_dump.sql.gz

if [ -f opencaching_dump.sql ]; then
    label "Import SQL Dump"
    mysql -uroot -proot < opencaching_dump.sql

    label "Run database and cache updates"
    cd /var/www/html/ && php bin/dbupdate.php

    label "Install OKAPI"
    curl http://local.opencaching.de/okapi/update?install=true
else
    errorLabel "Could not download or unpack sql dump from '$DUMP_URL'\n\n"
    exit 1;
fi

label "updating database structures ..."
cd /var/www/html

php bin/dbsv-update.php

if [ -f "sql/stored-proc/maintain.php" ]; then
    label "reinstall triggers (new) ..."
    cd sql/stored-proc
    php maintain.php
    cd ../..
elif [ -f "htdocs/doc/sql/stored-proc/maintain.php" ]; then
    label "-- reinstall triggers (old) ..."
    cd htdocs/doc/sql/stored-proc
    php maintain.php
    cd ../../../..
else
    label "error: maintain.php not found"
fi

if [ -f "sql/static-data/data.sql" ]; then
  label "importing static data (new) ..."
  mysql -u root -hlocalhost -proot opencaching < sql/static-data/data.sql
elif [ -f "htdocs/doc/sql/static-data/data.sql" ]; then
  echo "-- importing static data (old) ..."
  mysql -u root -hlocalhost -proot opencaching < htdocs/doc/sql/static-data/data.sql
else
  echo "error: data.sql not found"
  exit
fi

label "symfony migrations ..."
chmod 755 ./htdocs/bin/console
./htdocs/bin/console doctrine:migrations:migrate -n

echo "-- updating OKAPI database ..."
php bin/okapi-update.php|grep -i -e current -e mutation

echo "export PS1='\[\033[38;5;11m\]OCdev:\[$(tput sgr0)\]\[\033[38;5;15m\] \[$(tput sgr0)\]\[\033[38;5;14m\]\w\[$(tput sgr0)\]\[\033[38;5;15m\]\\$ \[$(tput sgr0)\]'" >> /home/vagrant/.bashrc
echo "cd /var/www/html/" >> /home/vagrant/.bashrc
echo "alias la='ls -alh'" >> /home/vagrant/.bashrc
echo "alias ..='cd ..'" >> /home/vagrant/.bashrc

label "All done, have fun."
