<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

if (($opt['db']['maintenance_password'] != '') && ($opt['debug'] && DEBUG_NO)) {
    die("ERROR: db maintenance password must not be configured in production enviroment!\n");
}

if (!$opt['logic']['node']['id']) {
    die("Node ID must be set. Also check \$oc_nodeid in lib/settings.inc.php.\n");
}

if ($opt['page']['https']['mode'] == HTTPS_DISABLED &&
    ($opt['page']['https']['is_default'] || $opt['page']['https']['force_login'])
) {
    die("inconsistent HTTPS settings\n");
}
