#!/bin/bash
#
# Opencaching replication monitor bash script
#
# This script writes every 10 seconds the current timestamp to
# table sys_repl_timestamp. This enables the cron-module
# repliaction_monitor to check if the mysql replication slave(s) is up to
# date and online.
#
# You should place this bash script outside the PHP configured
# open_basedir restriction and place a cronjob entry that
# executes every 5 minutes or place it in rc.3 or rc.5
# (run this script on the master database server, not on any slave!)
#
# If you setup a cronjob call it with parameter "-q" to prevent
# output of running-message.
#

# begin of configuration
PIDFILE=/var/run/oc_replication_monitor.pid
DBHOST=oc
DBNAME=oc
DBUSER=oc
DBPASSWORD=oc
# end of configuration

if [ -f $PIDFILE ]; then
if [ -d /proc/`cat $PIDFILE` ]; then
if (readlink /proc/`cat $PIDFILE`/exe | grep -q /bin/bash); then
if [ "$1" != "-q" ]; then
echo "replication_monitor running with pid `cat $PIDFILE`, exiting"
      fi
exit
fi
fi
fi

echo $$ > $PIDFILE

while [ 1 ]
do
mysql -h$DBHOST -u$DBUSER -p$DBPASSWORD $DBNAME --execute="INSERT INTO sys_repl_timestamp (id, data) VALUES (1, NOW()) ON DUPLICATE KEY UPDATE data=NOW();"
  sleep 10
done