<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once __DIR__ . '/./lib2/web.inc.php';

$tpl->name = 'mystatpic';
$tpl->menuitem = MNU_MYPROFILE_DATA_STATPIC;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect('login.php?target=mystatpic.php');
}

$tpl->display();
