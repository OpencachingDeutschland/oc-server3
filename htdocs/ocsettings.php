<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

    require_once('lib2/web.inc.php');
    require_once('lib2/logic/user.class.php');
    require_once('lib2/editSettings.class.php');
    require_once('lib2/edithelper.inc.php');

    $tpl->name = 'ocsettings';
    $tpl->menuitem = MNU_MYPROFILE_DETAILS;

    $login->verify();
    if ($login->userid == 0)
        $tpl->redirect_login();

    include('settingsmenu.php');

    if (isset($_REQUEST['cancel']))
    $tpl->redirect('mydetails.php');

    $action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : 'view';
    if ($action != 'change' && $action != 'changetext' && $action != 'view') $action = 'view';

    //created an array to display muliple optionids in the future
    $ocsettings_array = array(4);

    $settings = new editSettings();

    if ($action == 'change'){
        $settings->change($ocsettings_array,$tpl->name);
    }
    else{
        $settings->display($ocsettings_array);
    }

    $tpl->display();

exit;