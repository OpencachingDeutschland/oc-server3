
prerequsites:
old database name ocpl (if not, all scripts have to be changed to refer to other <database>.<table> instead)
new database name ocse (this name is only present in this readme and in the settings)

switch all mail-sending off for the server to prevent errors. on wamp-server this is done by commenting a few lines
about mail in php.ini.

this doc uses cli-access to the database, most tasks can be done through phomysql except big imports
this example have the source on D:\proj\oc\ocsemerge\se2degit\

create a new database: ocse, collation utf8_general_ci
grant if not already
create a full table insert:
cat *.sql > x.txt in the directory code\htdocs\doc\sql\tables
use script access through:
..\wamp\bin\mysql\mysql5.1.36\bin\mysql -u root ocse

load all tables:
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\tables\x.txt

load updates (these might already be in x.txt at the time of applying this!!!):
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\structure\01_longer_password.sql
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\structure\02_podcache.sql
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\structure\03_coordinates.sql

load static data:
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\static-data\data.sql

load all triggers:
For a clean database the triggers does not exist and the script fails. Open
doc/sql/stored-proc/maintain.php
and edit, comment sql_dropFunction and sql_dropProcedure
do the same for distance.php
http://localhost:8080/doc/sql/stored-proc/maintain.php
http://localhost:8080/doc/sql/stored-proc/distance.php
(this might involve setting a the correct username password in code\htdocs\util\mysql_root\setting.inc.php)
restore the two files maintain.php and distance.php.

upgrade old database into new:
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\data\00.01_new_cache_types.sql
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\data\01_copy_users.sql
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\data\02_copy_caches.sql
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\data\03_copy_misc.sql
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\data\04_copy_waypoint_and_note.sql
source D:\proj\oc\ocsemerge\se2degit\code\htdocs\doc\sql\se2de-lift\data\10_podcache.sql
// source 11_upgrader pod This isn't written since no pods exist in old system!

reapply the triggers:
http://localhost:8080/doc/sql/stored-proc/maintain.php
http://localhost:8080/doc/sql/stored-proc/distance.php

if you are running a testserver, prevent passwords and e-mails to be stolen:
update user set password = null, email = null where username <> 'OlofL';
update user and set all email to null except yours

update logentries set logtext = null, details = null;
update email_user set from_email = null, to_email = null;

switch on mail-sending again
