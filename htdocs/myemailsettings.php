<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

    require_once('./lib2/web.inc.php');
    require_once('lib2/editSettings.class.php');

    $tpl->name = 'myemailsettings';
    $tpl->menuitem = MNU_MYPROFILE_EMAILSETTINGS;

    $login->verify();

    include('settingsmenu.php');

    if (isset($_REQUEST['cancel']))
        $tpl->redirect('myemailsettings.php');

    $action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : 'view';
    if ($action != 'change' &&  $action != 'changeemail' && $action != 'view')
        $action = 'view';

    if ($login->userid == 0)
    {
        if ($action == 'change' || $action == 'changeemail')
            $tpl->redirect('login.php?target=' . urlencode('myemailsettings.php?action=change'));
        else
            $tpl->redirect('login.php?target=myemailsettings.php');
    }

    //created an array to display muliple optionids in the future
    $ocsettings_array = array(5);

    $settings = new editSettings();

    if ($action == 'changeemail')
        $tpl->redirect('newemail.php');
    else if ($action == 'change'){
        $settings->change($ocsettings_array,$tpl->name);
    }
    else
        $settings->display($ocsettings_array);

    exit;

?>