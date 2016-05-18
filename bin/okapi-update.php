<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

/*
 * run okapi database update
 * needs 'short_open_tag = On' in php.ini
 *
 * You should normally NOT call this script directly, but via dbupdate.php
 * (or something similar on a production system). This ensures that
 * everything takes place in the right order.
 */

okapi_update();


function okapi_update()
{
    $GLOBALS['rootpath'] = __DIR__ . '/../htdocs/';
    require_once $GLOBALS['rootpath'] . 'okapi/facade.php';
    okapi\Facade::database_update();
    // This may not work properly if an OKAPI update mutation function relies
    // on catching exceptions. The cryptic error message
    //   "exception thrown without a stack frame in Unknown on line 0"
    // may appear (see http://code.google.com/p/opencaching-api/issues/detail?id=243).
    // Then, you must call OKAPI update manually via
    //   http://site-address/okapi/update
}
