<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
 
	$disable_verifyemail = true;
	require('./lib2/web.inc.php');

	$email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
	$code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : '';

	$tpl->name = 'activation';
	$tpl->menuitem = MNU_START_REGISTER_ACTIVATION;

	$tpl->assign('errorEMail', false);
	$tpl->assign('errorCode', false);
	$tpl->assign('errorAlreadyActivated', false);
	$tpl->assign('sucess', false);

	if (isset($_REQUEST['submit']))
	{
		$email_not_ok = is_valid_email_address($email) ? false : true;

		if ($email_not_ok == false)
		{
			$rs = sql("SELECT `user_id` `id`, `activation_code` `code` FROM `user` WHERE `email`='&1'", $email);

			if ($r = sql_fetch_array($rs))
			{
				if (($r['code'] == $code) && ($code != ''))
				{
					// ok, account aktivieren
					sql("UPDATE `user` SET `is_active_flag`=1, `activation_code`='' WHERE `user_id`='&1'", $r['id']);
					$tpl->assign('sucess', true);
				}
				else
				{
					if ($r['code'] == '')
						$tpl->assign('errorAlreadyActivated', true);
					else
						$tpl->assign('errorCode', true);
				}
			}
			else
				$tpl->assign('errorCode', true);
			sql_free_result($rs);
		}
		else
			$tpl->assign('errorEMail', true);
	}

	$tpl->assign('email', $email);
	$tpl->assign('code', $code);

	$tpl->display();
?>