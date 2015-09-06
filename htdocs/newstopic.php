<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/OcHTMLPurifier.class.php');

	$tpl->name = 'newstopic';
	$tpl->menuitem = MNU_START_NEWS_POST;
	
	require($opt['rootpath'] . 'lib2/logic/captcha.inc.php');
	require($opt['rootpath'] . 'lib2/mail.class.php');

	$topicid = isset($_REQUEST['topic']) ? $_REQUEST['topic'] : 1;
	$newstext = isset($_REQUEST['newstext']) ? $_REQUEST['newstext'] : '';
	$newshtml = isset($_REQUEST['newshtml']) ? $_REQUEST['newshtml']+0 : 0;
	$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
	$captcha_id = isset($_REQUEST['captcha_id']) ? $_REQUEST['captcha_id'] : '';
	$captcha = isset($_REQUEST['captcha']) ? $_REQUEST['captcha'] : '';

	$emailok = false;
	$tpl->assign('email_error', 0);
	$tpl->assign('captcha_error', 0);
	$tpl->assign('confirm', 0);

	if (isset($_REQUEST['submit']))
	{
		$emailok = is_valid_email_address($email) ? true : false;
		$captchaok = checkCaptcha($captcha_id, $captcha);

		if ($emailok == true && $captchaok == true)
		{
			// filtern und ausgabe vorbereiten
			$tpl->assign('confirm', 1);

			if ($newshtml == 0)
				$newstext = htmlspecialchars($newstext, ENT_COMPAT, 'UTF-8');
			else
			{
				$purifier = new OcHTMLPurifier($opt);
				$newstext = $purifier->purify($newstext);
			}

			$sTopic = sql_value("SELECT `name` FROM `news_topics` WHERE `id`='&1'", '', $topicid);
			$tpl->assign('newstopic', $sTopic);
			$tpl->assign('newstext', $newstext);

			// in DB schreiben
			sql("INSERT INTO `news` (`content`, `topic`, `display`) VALUES ('&1', '&2', '&3')", $newstext, $topicid, 0);

			$rs = sql("SELECT `email` FROM `user` WHERE `admin`\\&'&1'='&1'", ADMIN_USER);
			while ($r = sql_fetch_assoc($rs))
			{
				// send confirmation
				$mail = new mail();
				$mail->name = 'newstopic';
				$mail->to = $r['email'];
				$mail->subject = $translate->t('A newsentry was created on opencaching', '', basename(__FILE__), __LINE__);
				$mail->assign('email', $email);
				$mail->assign('newstopic', $sTopic);
				$mail->assign('newstext', $newstext);
				$mail->send();
			}
			sql_free_result($rs);

			// erfolg anzeigen
			$tpl->display();
			exit;
		}
		
		if ($emailok != true)
			$tpl->assign('email_error', 1);
		if ($captchaok != true)
			$tpl->assign('captcha_error', 1);
	}
	
	$tpl->assign('newstext', $newstext);
	$tpl->assign('newshtml', $newshtml);
	$tpl->assign('email', $email);
	$tpl->assign('topic', $topicid);
	
	// topics erstellen
	$rs = sql("SELECT `id`, `name` FROM `news_topics` ORDER BY `id` ASC");
	$tpl->assign_rs('newsTopics', $rs);
	sql_free_result($rs);

	// captcha
	$captcha = createCaptcha();
	$tpl->assign('captcha_id', $captcha['id']);
	$tpl->assign('captcha_filename', $captcha['filename']);

	//make the template and send it out
	$tpl->display();
?>