<?php
/***************************************************************************
 *  Unicode Reminder メモ
 *
 *  Sample settincs.inc.php file for a developer machine
 ***************************************************************************/

    // installation paths
    $dev_basepath = '/var/www/html/';
    $dev_codepath = 'oc-server/server-3.0/';
    $dev_baseurl  = 'http://local.opencaching.de/oc-server/server-3.0/htdocs';

    // common developer system settings
    require("settings-dev.inc.php");

    // database access
    $opt['db']['servername'] = 'localhost';
    $opt['db']['username'] = 'oc';
    $opt['db']['password'] = 'developer';
    $opt['db']['pconnect'] = false;

    $opt['db']['maintenance_user'] = 'root';
    $opt['db']['maintenance_password'] = 'developer';

    // database names
    $opt['db']['placeholder']['db'] = 'opencaching';
    $opt['db']['placeholder']['tmpdb'] = 'octmp';

    // Blog news on start page
    // $opt['news']['count'] = 6;
    // $opt['news']['include'] = 'http://blog.opencaching.de/feed';

    // Forum topics on start page
    // $opt['forum']['count'] = 8;
    // $opt['forum']['url'] = 'http://forum.opencaching.de/index.php?action=.xml;type=rss;limit=25';
    // $opt['cron']['phpbbtopics']['name'] = 'forum.forum.opencaching.de';
;
