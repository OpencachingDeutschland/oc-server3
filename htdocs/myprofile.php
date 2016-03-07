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

	$tpl->name = 'myprofile';
	$tpl->menuitem = MNU_MYPROFILE_DATA;

	$login->verify();

	include('settingsmenu.php');

	$action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : 'view';
	if ($action != 'change' &&  $action != 'changeemail' && $action != 'view')
		$action = 'view';

	if ($login->userid == 0)
	{
		if ($action == 'change' || $action == 'changeemail')
			$tpl->redirect('login.php?target=' . urlencode('myprofile.php?action=change'));
		else
			$tpl->redirect('login.php?target=myprofile.php');
	}

	//created an array to display muliple optionids in the future
	$ocsettings_array = array(5);

	$settings = new editSettings();

	if ($action == 'change') {
        $settings->change($ocsettings_array,$tpl->name);
	}
	else{
        $settings->display($ocsettings_array);
	}


exit;


?>
