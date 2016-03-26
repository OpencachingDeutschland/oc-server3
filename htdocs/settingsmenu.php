<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

    $tpl->assign('set_profiledata', $tpl->name=="myprofile" );
    $tpl->assign('set_publicprofile', $tpl->name=="mydetails" );
    $tpl->assign('set_ocsettings', $tpl->name=="myocsettings" );
    $tpl->assign('set_email', $tpl->name=="myemailsettings"||$tpl->name=="newemail" );
    $tpl->assign('set_statpic', $tpl->name=="mystatpic" );
    $tpl->assign('set_pw', $tpl->name=="newpw" );
