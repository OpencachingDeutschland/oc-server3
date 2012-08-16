<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/mail.class.php');
require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');
require_once($opt['rootpath'] . 'lib2/logic/statpic.class.php');
require_once($opt['rootpath'] . 'lib2/logic/countriesList.class.php');
require_once($opt['rootpath'] . 'lib2/logic/cracklib.inc.php');
require_once($opt['rootpath'] . 'lib2/translate.class.php');

class user
{
	var $nUserId = 0;

	var $reUser;
	var $reUserStat;

	static function fromEMail($email)
	{
		$userid = sql_value("SELECT `user_id` FROM `user` WHERE `email`='&1'", 0, $email);
		if ($userid == 0)
			return null;

		return new user($userid);
	}

	static function fromUsername($username)
	{
		$userid = sql_value("SELECT `user_id` FROM `user` WHERE `username`='&1'", 0, $username);
		if ($userid == 0)
			return null;

		return new user($userid);
	}

	function __construct($nNewUserId=ID_NEW)
	{
		$this->reUser = new rowEditor('user');
		$this->reUser->addPKInt('user_id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reUser->addString('username', '', false);
		$this->reUser->addString('password', null, true);
		$this->reUser->addString('email', null, true);
		$this->reUser->addFloat('latitude', 0, false);
		$this->reUser->addFloat('longitude', 0, false);
		$this->reUser->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reUser->addBoolean('is_active_flag', false, false);
		$this->reUser->addString('last_name', '', false);
		$this->reUser->addString('first_name', '', false);
		$this->reUser->addString('country', null, true);
		$this->reUser->addBoolean('pmr_flag', false, false);
		$this->reUser->addString('new_pw_code', null, true);
		$this->reUser->addDate('new_pw_date', null, true);
		$this->reUser->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reUser->addString('new_email_code', null, true);
		$this->reUser->addDate('new_email_date', null, true);
		$this->reUser->addString('new_email', null, true);
		$this->reUser->addString('uuid', '', false, RE_INSERT_OVERWRITE|RE_INSERT_UUID);
		$this->reUser->addBoolean('permanent_login_flag', false, false);
		$this->reUser->addInt('watchmail_mode', 1, false);
		$this->reUser->addInt('watchmail_hour', 0, false);
		$this->reUser->addDate('watchmail_nextmail', time(), false);
		$this->reUser->addInt('watchmail_day', 0, false);
		$this->reUser->addString('activation_code', '', false);
		$this->reUser->addBoolean('no_htmledit_flag', false, false);
		$this->reUser->addInt('notify_radius', 0, false);
		$this->reUser->addInt('admin', 0, false);
		$this->reUser->addInt('node', 0, false);

		$this->reUserStat = new rowEditor('stat_user');
		$this->reUserStat->addPKInt('user_id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reUserStat->addInt('found', 0, false);
		$this->reUserStat->addInt('notfound', 0, false);
		$this->reUserStat->addInt('note', 0, false);
		$this->reUserStat->addInt('hidden', 0, false);

		$this->nUserId = $nNewUserId+0;

		if ($nNewUserId == ID_NEW)
		{
			$this->reUser->addNew(null);
		}
		else
		{
			$this->reUser->load($this->nUserId);
		}
	}

	function exist()
	{
		return $this->reUser->exist();
	}

	static function existUsername($username)
	{
		return (sql_value("SELECT COUNT(*) FROM `user` WHERE `username`='&1'", 0, $username) != 0);
	}

	static function existEMail($email)
	{
		return (sql_value("SELECT COUNT(*) FROM `user` WHERE `email`='&1'", 0, $email) != 0);
	}

	function getUserId()
	{
		return $this->nUserId;
	}

	function getUsername()
	{
		return $this->reUser->getValue('username');
	}
	function setUsername($value)
	{
		if (!mb_ereg_match(REGEX_USERNAME, $value))
			return false;

		if (is_valid_email_address($value))
			return false;

		return $this->reUser->setValue('username', $value);
	}
	function getUsernameChanged()
	{
		return $this->reUser->getChanged('username');
	}
	function getEMail()
	{
		return $this->reUser->getValue('email');
	}
	function setEMail($value)
	{
		if (!is_valid_email_address($value))
			return false;

		return $this->reUser->setValue('email', $value);
	}
	function getPassword()
	{
		return $this->reUser->getValue('password');
	}
	function setPassword($value)
	{
		global $opt;
	
		if (!mb_ereg_match(REGEX_PASSWORD, $value))
			return false;

		if (cracklib_checkPW($value, array('open', 'caching', $this->getUsername(), $this->getFirstName(), $this->getLastName())) == false)
			return false;

		$pwmd5 = md5($value);
		if ($opt['logic']['password_hash'])
			$pwmd5 = hash('sha512', $pwmd5);

		return $this->reUser->setValue('password', $pwmd5);
	}
	function getFirstName()
	{
		return $this->reUser->getValue('first_name');
	}
	function setFirstName($value)
	{
		if ($value != '')
			if (!mb_ereg_match(REGEX_FIRST_NAME, $value))
				return false;

		return $this->reUser->setValue('first_name', $value);
	}
	function getLastName()
	{
		return $this->reUser->getValue('last_name');
	}
	function setLastName($value)
	{
		if ($value != '')
			if (!mb_ereg_match(REGEX_LAST_NAME, $value))
				return false;

		return $this->reUser->setValue('last_name', $value);
	}
	function getCountry()
	{
		global $opt;
		return countriesList::getCountryLocaleName($this->reUser->getValue('country'));
	}
	function getCountryCode()
	{
		return $this->reUser->getValue('country');
	}
	function setCountryCode($value)
	{
		if ($value !== null && (sql_value("SELECT COUNT(*) FROM countries WHERE short='&1'", 0, $value) == 0))
			return false;

		return $this->reUser->setValue('country', $value);
	}
	function getLatitude()
	{
		return $this->reUser->getValue('latitude');
	}
	function setLatitude($value)
	{
		if (($value+0) > 90 || ($value+0) < -90)
			return false;

		return $this->reUser->setValue('latitude', $value+0);
	}
	function getLongitude()
	{
		return $this->reUser->getValue('longitude');
	}
	function setLongitude($value)
	{
		if (($value+0) > 180 || ($value+0) < -180)
			return false;

		return $this->reUser->setValue('longitude', $value+0);
	}
	function getNotifyRadius()
	{
		return $this->reUser->getValue('notify_radius');
	}
	function setNotifyRadius($value)
	{
		if (($value+0) < 0 || ($value+0) > 150)
			return false;
		return $this->reUser->setValue('notify_radius', $value+0);
	}
	function getPermanentLogin()
	{
		return $this->reUser->getValue('permanent_login_flag');
	}
	function setPermanentLogin($value)
	{
		return $this->reUser->setValue('permanent_login_flag', $value);
	}
	function getNoHTMLEditor()
	{
		return $this->reUser->getValue('no_htmledit_flag');
	}
	function setNoHTMLEditor($value)
	{
		return $this->reUser->setValue('no_htmledit_flag', $value);
	}
	function getUsePMR()
	{
		return $this->reUser->getValue('pmr_flag');
	}
	function setUsePMR($value)
	{
		return $this->reUser->setValue('pmr_flag', $value);
	}
	function getIsActive()
	{
		return $this->reUser->getValue('is_active_flag');
	}
	function setIsActive($value)
	{
		return $this->reUser->setValue('is_active_flag', $value);
	}
	function getActivationCode()
	{
		return $this->reUser->getValue('activation_code');
	}
	function setActivationCode($value)
	{
		return $this->reUser->setValue('activation_code', $value);
	}
	function getNewPWCode()
	{
		return $this->reUser->getValue('new_pw_code');
	}
	function setNewPWCode($value)
	{
		return $this->reUser->setValue('new_pw_code', $value);
	}
	function getNewPWDate()
	{
		return $this->reUser->getValue('new_pw_date');
	}
	function setNewPWDate($value)
	{
		return $this->reUser->setValue('new_pw_date', $value);
	}
	function getNewEMailCode()
	{
		return $this->reUser->getValue('new_email_code');
	}
	function setNewEMailCode($value)
	{
		return $this->reUser->setValue('new_email_code', $value);
	}
	function getNewEMailDate()
	{
		return $this->reUser->getValue('new_email_date');
	}
	function setNewEMailDate($value)
	{
		return $this->reUser->setValue('new_email_date', $value);
	}
	function getNewEMail()
	{
		return $this->reUser->getValue('new_email');
	}
	function setNewEMail($value)
	{
		if ($value !== null)
		{
			if (!is_valid_email_address($value))
				return false;

			if (user::existEMail($value))
				return false;
		}

		return $this->reUser->setValue('new_email', $value);
	}
	function getWatchmailMode()
	{
		return $this->reUser->getValue('watchmail_mode');
	}
	function setWatchmailMode($value)
	{
		$this->setWatchmailNext('0000-00-00 00:00:00');
		return $this->reUser->setValue('watchmail_mode', $value);
	}
	function getWatchmailHour()
	{
		return $this->reUser->getValue('watchmail_hour');
	}
	function setWatchmailHour($value)
	{
		$this->setWatchmailNext('0000-00-00 00:00:00');
		return $this->reUser->setValue('watchmail_hour', $value);
	}
	function getWatchmailDay()
	{
		return $this->reUser->getValue('watchmail_day');
	}
	function setWatchmailDay($value)
	{
		$this->setWatchmailNext('0000-00-00 00:00:00');
		return $this->reUser->setValue('watchmail_day', $value);
	}
	function getWatchmailNext()
	{
		return $this->reUser->getValue('watchmail_nextmail');
	}
	function setWatchmailNext()
	{
		return $this->reUser->setValue('watchmail_nextmail', $value);
	}

	function getStatFound()
	{
		if ($this->reUserStat->exist())
			return $this->reUserStat->getValue('found');
		else
			return 0;
	}
	function getStatNotFound()
	{
		if ($this->reUserStat->exist())
			return $this->reUserStat->getValue('notfound');
		else
			return 0;
	}
	function getStatNote()
	{
		if ($this->reUserStat->exist())
			return $this->reUserStat->getValue('note');
		else
			return 0;
	}
	function getStatHidden()
	{
		if ($this->reUserStat->exist())
			return $this->reUserStat->getValue('hidden');
		else
			return 0;
	}
	function getDateRegistered()
	{
		return $this->reUser->getValue('date_created');
	}
	function getUUID()
	{
		return $this->reUser->getValue('uuid');
	}
	function getLastModified()
	{
		return $this->reUser->getValue('last_modified');
	}
	function getDateCreated()
	{
		return $this->reUser->getValue('date_created');
	}
	function getAdmin()
	{
		return $this->reUser->getValue('admin');
	}
	function getNode()
	{
		return $this->reUser->getValue('node');
	}
	function setNode($value)
	{
		return $this->reUser->setValue('node', $value);
	}

	function getAnyChanged()
	{
		return $this->reUser->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		$bNeedStatpicClear = $this->reUser->getChanged('username');

		if ($this->reUser->save())
		{
			$this->getStatpic()->invalidate();
			sql_slave_exclude();
			return true;
		}
		else
			return false;
	}

	function getStatpic()
	{
		return new statpic($this->nUserId);
	}

	static function createCode()
	{
		return mb_strtoupper(mb_substr(md5(uniqid('')), 0, 13));
	}

	function requestNewPWCode()
	{
		global $translate;

		if (!$this->exist())
			return false;

		$email = $this->getEMail();
		if ($email === null || $email == '')
			return false;

		if (!$this->getIsActive())
			return false;

		$this->setNewPWCode($this->createCode());
		if (!$this->reUser->saveField('new_pw_code'))
			return false;

		$this->setNewPWDate(time());
		if (!$this->reUser->saveField('new_pw_date'))
			return false;

		// send confirmation
		$mail = new mail();
		$mail->name = 'newpw';
		$mail->to = $email;
		$mail->subject = $translate->t('New password code', '', basename(__FILE__), __LINE__);
		$mail->assign('code', $this->getNewPWCode());
		$mail->send();

		return true;
	}

	function clearNewPWCode()
	{
		$this->setNewPWCode(null);
		if (!$this->reUser->saveField('new_pw_code'))
			return false;

		$this->setNewPWDate(null);
		if (!$this->reUser->saveField('new_pw_date'))
			return false;

		return true;
	}

	function requestNewEMail($email)
	{
		global $translate;

		if (!$this->exist())
			return false;

		if (mb_strtolower($this->getEMail()) == mb_strtolower($email))
			return false;

		if ($this->getEMail() === null || $this->getEMail() == '')
			return false;

		if (!$this->getIsActive())
			return false;

		$this->setNewEMailCode($this->createCode());
		if (!$this->reUser->saveField('new_email_code'))
			return false;

		$this->setNewEMailDate(time());
		if (!$this->reUser->saveField('new_email_date'))
			return false;

		$this->setNewEMail($email);
		if (!$this->reUser->saveField('new_email'))
			return false;

		// send confirmation
		$mail = new mail();
		$mail->name = 'newemail';
		$mail->to = $email;
		$mail->subject = $translate->t('New email code', '', basename(__FILE__), __LINE__);
		$mail->assign('code', $this->getNewEMailCode());
		$mail->send();

		return true;
	}

	function clearNewEMailCode()
	{
		$this->setNewEMailCode(null);
		if (!$this->reUser->saveField('new_email_code'))
			return false;

		$this->setNewEMailDate(null);
		if (!$this->reUser->saveField('new_email_date'))
			return false;

		$this->setNewEMail(null);
		if (!$this->reUser->saveField('new_email'))
			return false;

		return true;
	}

	function remindEMail()
	{
		global $translate;

		if (!$this->exist())
			return false;

		$email = $this->getEMail();
		if ($email === null || $email == '')
			return false;

		if (!$this->getIsActive())
			return false;

		// send confirmation
		$mail = new mail();
		$mail->name = 'remindemail';
		$mail->to = $email;
		$mail->subject = $translate->t('Reminder to your E-Mail-Address', '', basename(__FILE__), __LINE__);
		$mail->assign('username', $this->getUsername());
		$mail->assign('email', $email);
		$mail->send();

		return true;
	}

	function sendRegistrationCode()
	{
		global $translate;

		$countriesList = new countriesList();

		$mail = new mail();
		$mail->name = 'register';
		$mail->to = $this->getEMail();
		$mail->subject = $translate->t('Registration confirmation', '', basename(__FILE__), __LINE__);
		$mail->assign('username', $this->getUsername());
		$mail->assign('last_name', $this->getLastName());
		$mail->assign('first_name', $this->getFirstName());
		$mail->assign('country', $countriesList->getCountryLocaleName($this->getCountryCode()));
		$mail->assign('code', $this->getActivationCode());

		if ($mail->send())
			return true;
		else
			return false;
	}

	function sendEMail($nFromUserId, $sSubject, $sText, $bSendEMailAddress)
	{
		global $opt, $translate;

		if ($this->exist() == false)
			return false;

		if ($this->getIsActive() == false)
			return false;

		if ($this->getEMail() === null || $this->getEMail() == '')
			return false;

		if ($sSubject == '')
			return false;

		if ($sText == '')
			return false;

		if (mb_strpos($sSubject, "\n") !== false)
			$sSubject = mb_substr($sSubject, 0, mb_strpos($sSubject, "\n"));
		$sSubject = mb_trim($sSubject);

		$fromUser = new user($nFromUserId);
		if ($fromUser->exist() == false)
			return false;
		if ($fromUser->getIsActive() == false)
			return false;
		if ($fromUser->getEMail() === null || $fromUser->getEMail() == '')
			return false;

		// ok, we can send ...
		$mail = new mail();
		$mail->name = 'usercontactmail';
		$mail->to = $this->getEMail();

		$mail->from = $opt['mail']['usermail'];

		if ($bSendEMailAddress == true)
		{
			$mail->replyTo = $fromUser->getEMail();
			$mail->returnPath = $fromUser->getEMail();
		}

		$mail->subject = $translate->t('E-Mail from', '', basename(__FILE__), __LINE__) . ' ' . $fromUser->getUsername() . ': ' . $sSubject;
		$mail->assign('usersubject', $sSubject);
		$mail->assign('text', $sText);
		$mail->assign('username', $this->getUsername());
		$mail->assign('sendemailaddress', $bSendEMailAddress);
		$mail->assign('fromusername', $fromUser->getUsername());
		$mail->assign('fromuserid', $fromUser->getUserId());
		$mail->assign('fromuseremail', $fromUser->getEMail());

		if ($mail->send())
		{
			// send copy to fromUser
			$mail->assign('copy', true);
			$mail->to = $fromUser->getEMail();
			$mail->send();

			// log
			sql("INSERT INTO `email_user` (`ipaddress`, 
			                               `from_user_id`, 
			                               `from_email`, 
			                               `to_user_id`, 
			                               `to_email`)
			                       VALUES ('&1', '&2', '&3', '&4', '&5')", 
			                               $_SERVER["REMOTE_ADDR"],
			                               $fromUser->getUserId(),
			                               $fromUser->getEMail(),
			                               $this->getUserId(),
			                               $this->getEMail());
			return true;
		}
		else
			return false;
	}

	function canDisable()
	{
		global $login;
		$login->verify();

		if ($login->userid != $this->nUserId && ($login->admin & ADMIN_USER) != ADMIN_USER)
			return false;

		if ($this->getIsActive() != 0)
			return true;
		else
			return false;
	}

	function disable()
	{
		global $login;

		if ($this->canDisable() == false)
			return false;

		// write old record to log
		$backup = array();
		$backup['username'] = $this->getUsername();
		$backup['email'] = $this->getEMail();
		$backup['last_name'] = $this->getLastName();
		$backup['first_name'] = $this->getFirstName();

		sql("INSERT INTO `logentries` (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`)
		                       VALUES ('user', 6, '&1', '&2', '&3', '&4', '&5')",
		                       $login->userid, $this->nUserId, 0, 
		                       'User ' . sql_escape($this->getUsername()) . ' disabled',
		                       serialize($backup));

		sql("UPDATE `caches` SET `status`=6 WHERE `user_id`='&1' AND `status` IN (1, 2, 3)", $this->nUserId);
		sql("UPDATE `user` SET `password`=NULL, `email`=NULL, 
		                       `is_active_flag`=0, 
		                       `latitude`=0, `longitude`=0, 
		                       `last_name`='', `first_name`='', 
		                       `country`=NULL, `new_pw_code`=NULL,
		                       `new_pw_date`=NULL, `new_email`=NULL,
		                       `new_email_code`=NULL, `activation_code`='',
		                       `notify_radius`=0, `statpic_text`=''
		                 WHERE `user_id`='&1'", $this->nUserId);
		$this->reload();

		return true;
	}

	function canDelete()
	{
		global $login;
		$login->verify();

		if ($login->userid != $this->nUserId && ($login->admin & ADMIN_USER) != ADMIN_USER)
			return false;

		if (sql_value("SELECT COUNT(*) FROM `caches` WHERE `user_id`='&1'", 0, $this->nUserId) > 0)
			return false;

		if (sql_value("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1'", 0, $this->nUserId) > 0)
			return false;

		return true;
	}

	function delete()
	{
		global $login;

		if ($this->canDelete() == false)
			return false;

		// write old record to log
		$backup = array();
		$backup['username'] = $this->getUsername();
		$backup['email'] = $this->getEMail();
		$backup['last_name'] = $this->getLastName();
		$backup['first_name'] = $this->getFirstName();

		sql("INSERT INTO `logentries` (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`)
		                       VALUES ('user', 7, '&1', '&2', '&3', '&4', '&5')",
		                       $login->userid, $this->nUserId, 0, 
		                       'User ' . sql_escape($this->getUsername()) . ' deleted',
		                       serialize($backup));

		sql("DELETE FROM `user` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `cache_adoption` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `cache_ignore` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `cache_rating` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `cache_watches` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `stat_user` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `user_options` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `watches_waiting` WHERE `user_id`='&1'", $this->nUserId);

		$this->reload();

		return true;
	}

	function reload()
	{
		$this->reUser->reload();
		$this->reUserStat->reload();
	}
}
?>