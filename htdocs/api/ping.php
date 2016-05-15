<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$opt['rootpath'] = '../';
require(__DIR__ . '/../lib2/web.inc.php');

header('Content-type: text/plain; charset=utf-8');
echo sql_value('SELECT NOW()', '');
