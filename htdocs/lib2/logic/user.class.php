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
require_once($opt['rootpath'] . 'lib2/logic/picture.class.php');
require_once($opt['rootpath'] . 'lib2/logic/cache.class.php');
require_once($opt['rootpath'] . 'lib2/logic/cracklib.inc.php');
require_once($opt['rootpath'] . 'lib2/logic/crypt.class.php');
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
		global $opt;

		$this->reUser = new rowEditor('user');
		$this->reUser->addPKInt('user_id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reUser->addString('username', '', false);
		$this->reUser->addString('password', null, true);
		$this->reUser->addString('email', null, true);
		$this->reUser->addString('email_problems', 0, false);
		$this->reUser->addDate('last_email_problem', null, true);
		$this->reUser->addInt('mailing_problems', 0, false);
		$this->reUser->addFloat('latitude', 0, false);
		$this->reUser->addFloat('longitude', 0, false);
		$this->reUser->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reUser->addBoolean('is_active_flag', false, false);
		$this->reUser->addString('last_name', '', false);
		$this->reUser->addString('first_name', '', false);
		$this->reUser->addString('country', null, true);
		$this->reUser->addBoolean('accept_mailing', false, false);
		$this->reUser->addBoolean('pmr_flag', false, false);
		$this->reUser->addString('new_pw_code', null, true);
		$this->reUser->addDate('new_pw_date', null, true);
		$this->reUser->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reUser->addString('new_email_code', null, true);
		$this->reUser->addDate('new_email_date', null, true);
		$this->reUser->addString('new_email', null, true);
		$this->reUser->addString('uuid', '', false, RE_INSERT_AUTOUUID);
		$this->reUser->addBoolean('permanent_login_flag', false, false);
		$this->reUser->addInt('watchmail_mode', 1, false);
		$this->reUser->addInt('watchmail_hour', 0, false);
		$this->reUser->addDate('watchmail_nextmail', time(), false);
		$this->reUser->addInt('watchmail_day', 0, false);
		$this->reUser->addString('activation_code', '', false);
		$this->reUser->addBoolean('no_htmledit_flag', false, false);
		$this->reUser->addBoolean('usermail_send_addr', false, false);
		$this->reUser->addInt('notify_radius', 0, false);
		$this->reUser->addInt('notify_oconly', 1, false);
		$this->reUser->addInt('admin', 0, false);
		$this->reUser->addInt('data_license', $opt['logic']['license']['newusers'], false);
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
			$this->reUserStat->load($this->nUserId);
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
	function setPassword($password)
	{
		if (!mb_ereg_match(REGEX_PASSWORD, $password))
			return false;

		if (cracklib_checkPW($password, array('open', 'caching', 'cache', $this->getUsername(), $this->getFirstName(), $this->getLastName())) == false)
			return false;

		$encryptedPassword = crypt::encryptPassword($password);

		return $this->reUser->setValue('password', $encryptedPassword);
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
	function getNotifyOconly()
	{
		return $this->reUser->getValue('notify_oconly') != 0;
	}
	function setNotifyOconly($value)
	{
		return $this->reUser->setValue('notify_oconly', $value ? 1 : 0);
	}
	function getPermanentLogin()
	{
		return $this->reUser->getValue('permanent_login_flag');
	}
	function setPermanentLogin($value)
	{
		return $this->reUser->setValue('permanent_login_flag', $value);
	}
	function getAccMailing()
	{
		return $this->reUser->getValue('accept_mailing');
	}
	function setAccMailing($value)
	{
		return $this->reUser->setValue('accept_mailing', $value);
	}
	function getUsermailSendAddress()
	{
		return $this->reUser->getValue('usermail_send_addr');
	}
	function setUsermailSendAddress($value)
	{
		return $this->reUser->setValue('usermail_send_addr', $value);
	}
	function getNoWysiwygEditor()
	{
		return $this->reUser->getValue('no_htmledit_flag');
	}
	function setNoWysiwygEditor($value)
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
	function setWatchmailNext($value)
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

		sql_slave_exclude();
		if ($this->reUser->save())
		{
			if ($this->getUserId() == ID_NEW)
				$this->nUserId = $this->reUser->getValue('user_id');
			$this->getStatpic()->invalidate();
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
		global $opt, $translate;

		$countriesList = new countriesList();

		$mail = new mail();
		$mail->name = 'register';
		$mail->to = $this->getEMail();
		$mail->subject = $translate->t('Registration confirmation', '', basename(__FILE__), __LINE__);
		$mail->assign('domain', $opt['page']['domain']);
		$mail->assign('activation_page', $opt['page']['absolute_url'] . 'activation.php');
		$mail->assign('short_activation_page', $opt['page']['absolute_url'] . 'a.php');
		$mail->assign('username', $this->getUsername());
		$mail->assign('userid', $this->getUserId());
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
		global $login, $translate;

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

		// delete private data
		sql("UPDATE `user` SET `password`=NULL, `email`=NULL, 
		                       `is_active_flag`=0, 
		                       `latitude`=0, `longitude`=0, 
		                       `last_name`='', `first_name`='', `country`=NULL, `accept_mailing`=0, `pmr_flag`=0,
		                       `new_pw_code`=NULL, `new_pw_date`=NULL,
		                       `new_email`=NULL, `new_email_code`=NULL, `new_email_date`=NULL,
		                       `email_problems`=0, `first_email_problem`=NULL, `last_email_problem`=NULL,
		                       `permanent_login_flag`=0, `activation_code`='',
		                       `notify_radius`=0
		                 WHERE `user_id`='&1'", $this->nUserId);
			// Statpic and profile description texts are published under the data license
			// terms and therefore need not to be deleted.
		sql("DELETE FROM `user_options` WHERE `user_id`='&1'", $this->nUserId);
		$this->reload();

		sql("DELETE FROM `cache_lists`     WHERE `user_id`='&1'", $this->nUserId);  // Triggers will do all the dependent clean-up.
		sql("DELETE FROM `cache_adoption`  WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `cache_ignore`    WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `cache_watches`   WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `watches_waiting` WHERE `user_id`='&1'", $this->nUserId);
		sql("DELETE FROM `notify_waiting`  WHERE `user_id`='&1'", $this->nUserId);

		// lock the user's caches
		$error = false;
		$rs = sql("SELECT `cache_id` FROM `caches` WHERE `user_id`='&1' AND `status` IN (1,2,3)", $this->nUserId);
		while (($rCache = sql_fetch_assoc($rs)) && !$error)
		{
			$error = true;
			$cache = new cache($rCache['cache_id']);
			if ($cache->setStatus(6) && $cache->save())
			{
				$log = cachelog::createNew($rCache['cache_id'],$login->userid,true);
				if ($log !== false)
				{
					$log->setType(cachelog::LOGTYPE_LOCKED,true);
					$log->setOcTeamComment(true);
					$log->setDate(date('Y-m-d'));
					$log->setText($translate->t('The user account has been disabled.', '','',0,'',1, $cache->getDefaultDescLanguage()));
					$log->setTextHtml(false);
					if ($log->save())
						$error = false;
				}
			}
			echo "\n";
		}
		sql_free_result($rs);

		return !$error;
	}
	
	
	function canDisableDueLicense()
	{
		global $login;
		$login->verify();

		if ($login->userid != $this->nUserId && ($login->admin & ADMIN_USER) != ADMIN_USER)
			return false;
		else
			return true;
	}

	/**
	 * disables user (if not disabled), removes all licensed content from db and
	 * replaces every picture with a dummy one
	 * 
	 * @return string error message, if anything went wrong, true otherwise
	 *
	 * old_disabled: the user was disabled already before license transition
	 *               and therefore could not accept/decline the license
	 */
	function disduelicense($old_disabled=false) {
		
		// get translation-object
		global $translate;

		// check if disabled, disable if not
		if (!$this->canDisableDueLicense())
			return 'this user must not be disabled';

		if (!$old_disabled)
			if ($this->canDisable())
				if (!$this->disable())
					return 'disable user failed';

		// remember that data license was declined
		sql("UPDATE user SET data_license='&2' WHERE user_id='&1'", 
		    $this->getUserId(),
		    $old_disabled ? NEW_DATA_LICENSE_PASSIVELY_DECLINED : NEW_DATA_LICENSE_ACTIVELY_DECLINED);

		/*
		 * set all cache_desc and hint to '', save old texts
		 */
		// check if there are caches
		$num_caches = sql_value("SELECT COUNT(*) FROM `caches` WHERE `user_id`='&1'", 
														0, $this->getUserId());
		if ($num_caches > 0) {
			$cache_descs = array();
			$rs = sql("SELECT `id`, `language`, `desc`, `hint` " .
								"FROM `cache_desc`,`caches` " .
								"WHERE `caches`.`cache_id`=`cache_desc`.`cache_id` " .
								"AND `caches`.`user_id`='&1'",
								$this->getUserId()
					);
			while ($cache_desc = sql_fetch_array($rs,MYSQL_ASSOC)) 
				$cache_descs[] = $cache_desc;
			sql_free_result($rs);	

			// walk through cache_descs and set message for each language
			foreach ($cache_descs as $desc)
			{ 
				// save text - added 2013/03/18 to be enable restoring data on reactivation
				// of accounts that were disabled before license transition
				if ($desc['desc'] != "")
					sql("INSERT IGNORE INTO `saved_texts` (`object_type`, `object_id`, `subtype`, `text`)
					     VALUES ('&1', '&2', '&3', '&4')",
					     OBJECT_CACHEDESC, $desc['id'], 1, $desc['desc'] );
				if ($desc['hint'] != "")
					sql("INSERT IGNORE INTO `saved_texts` (`object_type`, `object_id`, `subtype`, `text`)
					     VALUES ('&1', '&2', '&3', '&4')",
					     OBJECT_CACHEDESC, $desc['id'], 2, $desc['hint'] );

				if ($desc['desc'] != "")
					if ($old_disabled)
						$descmsg = $translate->t("cache description was removed because the owner's account was inactive when the <a href='articles.php?page=impressum#datalicense'>new content license</a> was launched", '',  basename(__FILE__), __LINE__, '', 1, $desc['language']);
					else
						$descmsg = $translate->t('cache description was removed because owner declined content license', '',  basename(__FILE__), __LINE__, '', 1, $desc['language']);
				else
					$descmsg = "";

				sql("UPDATE `cache_desc` " .
						"SET `desc`='&1',`hint`='&2' " .
						"WHERE `id`='&3'",
						"<em>" . $descmsg . "</em>", 
						'',
						$desc['id']
					 );
			}

			// replace pictures
			$errmesg = $this->replace_pictures(OBJECT_CACHE);
			if ($errmesg !== true) 
				return "removing cache pictures: $errmesg";
		}

		// delete additional waypoint texts
		$rs = sql("SELECT `id`, `description` FROM `coordinates`
		           WHERE `type`='&1'
	             AND `cache_id` IN (SELECT `cache_id` FROM `caches` WHERE `user_id`='&2')",
				      COORDINATE_WAYPOINT, $this->getUserId());
		while ($wp = sql_fetch_assoc($rs))
		{
			if ($wp['description'] != "")
				sql("INSERT IGNORE INTO `saved_texts` (`object_type`, `object_id`, `subtype`, `text`)
				     VALUES ('&1', '&2', '&3', '&4')",
				    OBJECT_WAYPOINT, $wp['id'], 0, $wp['description'] );

			sql("UPDATE `coordinates` SET `description`=''
		       WHERE `id`='&1'",
			    $wp['id']);
		}
		sql_free_result($rs);
		
		/*
		 * set all cache_logs '', save old texts and delete pictures
		 */
		$rs = sql("SELECT `id`, `text`
							 FROM `cache_logs`
							 WHERE `user_id`='&1'",
							$this->getUserId()
						 );
		while ($log = sql_fetch_array($rs,MYSQL_ASSOC))
		{
			// save text - added 2013/03/18 to be enable restoring data on reactivation
			// of accounts that were disabled before license transition
			sql("INSERT IGNORE INTO `saved_texts` (`object_type`, `object_id`, `subtype`, `text`)
			     VALUES ('&1', '&2', '&3', '&4')",
			     OBJECT_CACHELOG, $log['id'], 0, $log['text']);

			// set text ''
			sql("UPDATE `cache_logs` SET `text`='' WHERE `id`='&1'", $log['id']);
			
			/*
			// replace pictures
			$errmesg = $this->replace_pictures(OBJECT_CACHELOG);
			if ($errmesg !== true) 
				return "removing log pictures: $errmesg";
			*/

			// delete log pictures
			$rsp = sql("SELECT `id` FROM `pictures`
			            WHERE `object_type`='&1' AND `object_id`='&2'",
			            OBJECT_CACHELOG, $log['id']);
			while ($pic = sql_fetch_assoc($rsp))
			{
				$picture = new picture($pic['id']);
				$picture->delete();
			}
			sql_free_result($rsp);
		}
		sql_free_result($rs);

		// discard achived logs' texts
		sql("UPDATE `cache_logs_archived` SET `text`='' WHERE `user_id`='&1'", $this->getUserId());
		
		// success
		return true;
	}
	
	/**
	 * replaces all pictures of $this-user with a dummy for the given object-type
	 * @param int $object_type object_types-id from table object_types
	 * @return bool true, if replacement worked, false otherwise
	 */
	function replace_pictures($object_type) {
		
		// get optionsarray
		global $opt;
		
		// load bmp-support
		require_once($opt['rootpath'] . 'lib2/imagebmp.inc.php');
		
		// paths cleared by trailing '/'
		if (substr($opt['logic']['pictures']['dir'],-1) != '/') 
			$picpath = $opt['logic']['pictures']['dir'];
		else 
			$picpath = substr($opt['logic']['pictures']['dir'],0,-1);

		$thumbpath = "$picpath/thumbs";

		$pdummy = isset($opt['logic']['pictures']['dummy']);		
		if ($pdummy && isset($opt['logic']['pictures']['dummy']['bgcolor']) && is_array($opt['logic']['pictures']['dummy']['bgcolor'])) 
			$dummybg = $opt['logic']['pictures']['dummy']['bgcolor'];
		else 
			$dummybg = array(255,255,255);
		
		if ($pdummy && isset($opt['logic']['pictures']['dummy']['text'])) 
			$dummytext = $opt['logic']['pictures']['dummy']['text'];
		else 
			$dummytext = '';
		
		if ($pdummy && isset($opt['logic']['pictures']['dummy']['textcolor']) && is_array($opt['logic']['pictures']['dummy']['textcolor'])) 
			$dummytextcolor = $opt['logic']['pictures']['dummy']['textcolor'];
		else 
			$dummytextcolor = array(0,0,0);
		
		$tmh = 0;
		$tmw = 0;
		
		/*
		 * check log or cache
		 */
		if ($object_type == OBJECT_CACHE) 
			// get filenames of the pictures of $this' caches
			$rs = sql("SELECT `pictures`.`url` " .
								"FROM `pictures`,`caches` " .
								"WHERE `caches`.`cache_id`=`pictures`.`object_id`" .
								" AND `pictures`.`object_type`='&1' AND `caches`.`user_id`='&2'",
								OBJECT_CACHE, $this->getUserId()
							);
			
		elseif ($object_type == OBJECT_CACHELOG) 
			// get filenames of the pictures of $this' logs
			$rs = sql("SELECT `pictures`.`url` " .
								"FROM `pictures`,`cache_logs` " .
								"WHERE `cache_logs`.`id`=`pictures`.`object_id`" .
								" AND `pictures`.`object_type`='&1' AND `cache_logs`.`user_id`='&2'",
								OBJECT_CACHELOG, $this->getUserId()
							);
		
		// set thumb-dimensions
		$tmh = $opt['logic']['pictures']['thumb_max_height'];
		$tmw = $opt['logic']['pictures']['thumb_max_width'];
				
		$filenames = array();
		while ($url = sql_fetch_array($rs,MYSQL_NUM)) 
			$filenames[] = substr($url['url'],-40);
		
		// free result
		sql_free_result($rs);

		/*
		 * walk through filenames and replace original
		 */
		// check if there is something to replace
		if (count($filenames) > 0) {
			
			foreach ($filenames as $fn) {
				
				// get uuid and extension
				$uuid = substr($fn,0,36);
				$ext = substr($fn,-3);
				$thumb_dir1 = substr($uuid,0,1);
				$thumb_dir2 = substr($uuid,1,1);
				
				// read original size
				if (file_exists("$picpath/$fn")) {
					list($w,$h,$t,$attr) = getimagesize("$picpath/$fn");
				} else {
					$w = 600;
					$h = 480;
				}
				
				// create new image
				$im = imagecreatetruecolor($w,$h);
				// allocate colors
				$col_bg = imagecolorallocate($im,$dummybg[0],$dummybg[1],$dummybg[2]);
				$col_text = imagecolorallocate($im,$dummytextcolor[0],$dummytextcolor[1],$dummytextcolor[2]);
				
				// fill bg
				imagefill($im,0,0,$col_bg);
				
				// check for replacement-image
				if ($pdummy && isset($opt['logic']['pictures']['dummy']['replacepic']) 
			              && $opt['logic']['pictures']['dummy']['replacepic'] != $opt['rootpath'] . 'images/' 
										&& file_exists($opt['logic']['pictures']['dummy']['replacepic'])) {
					
					// get dimensions of the replacement
					list($rw,$rh,$rt,$rattr) = getimagesize($opt['logic']['pictures']['dummy']['replacepic']);
					$rwh = 0;
					if($rw > $rh) {
						$rwh = $rh;
					} else {
						$rwh = $rw;
					}
					
					// check dimensions of original and set replacement size
					$rsize = 0;
					if ($w > $h) {
						if(($h * 0.85) > $rwh) {
							$rsize = $rwh;
						} else {
							$rsize = $h * 0.9;
						}
					} else {
						if(($w * 0.85) > $rwh) {
							$rsize = $rwh;
						} else {
							$rsize = $w * 0.9;
						}
					}
					$dx = ($w-$rsize)/2;
					$dy = ($h-$rsize)/2;
					
					// get replacement image
					$rext = substr($opt['logic']['pictures']['dummy']['replacepic'],-3);
					$rim = null;
					if ($rext == 'jpg') {
						$rim = imagecreatefromjpeg($opt['logic']['pictures']['dummy']['replacepic']);
					} elseif ($rext == 'png') {
						$rim = imagecreatefrompng($opt['logic']['pictures']['dummy']['replacepic']);
					} elseif ($rext == 'gif') {
						$rim = imagecreatefromgif($opt['logic']['pictures']['dummy']['replacepic']);
					} elseif ($rext == 'bmp') {
						$rim = imagecreatefrombmp($opt['logic']['pictures']['dummy']['replacepic']);
					}
					
					// copy image
					if (!is_null($rim)) 
						imagecopyresampled($im, $rim, $dx, $dy, 0, 0, $rsize, $rsize, $rw, $rh);
					
				} else {
				
					// set text
					if ($dummytext != '') 
						imagestring($im,1,10,$h/2,$dummytext,$col_text);
					else {
						imageline($im,0,0,$w,$h,0xff0000);
						imageline($im,0,$h,$w,0,0xff0000);
					}
				}
				
				// save dummy
				if ($ext == 'jpg') {
					if (!imagejpeg($im,"$picpath/$fn",75)) 
						return "save dummy failed [$ext]";
				} elseif ($ext == 'png') {
					if (!imagepng($im,"$picpath/$fn",4)) 
						return "save dummy failed [$ext]";
				} elseif ($ext == 'gif') {
					if (!imagegif($im,"$picpath/$fn")) 
						return "save dummy failed [$ext]";
				} elseif ($ext == 'bmp') {
					if (!imagebmp($im,"$picpath/$fn")) 
						return "save dummy failed [$ext]";
				} else {
					return "save dummy failed [$ext], unknown extension";
				}
				
				// set thumb-dimensions
				if (($h > $tmh) || ($w > $tmw))
				{
					if ($h > $w)
					{
						$th = $tmh;
						$tw = $w * ($th / $h);
					}
					else
					{
						$tw = $tmw;
						$th = $h * ($tw / $w);
					}
				}
				else
				{
					$tw = $w;
					$th = $h;
				}
				
				// copy dummy
				$tim = imagecreatetruecolor($tw, $th);
				imagecopyresampled($tim, $im, 0, 0, 0, 0, $tw, $th, $w, $h);
				
				// check directories or create them
				if (!file_exists("$thumbpath/$thumb_dir1"))
					if (!mkdir("$thumbpath/$thumb_dir1")) {
						return 'mkdir in thumbpath failed';
				}
				if (!file_exists("$thumbpath/$thumb_dir1/$thumb_dir2")){
					if (!mkdir("$thumbpath/$thumb_dir1/$thumb_dir2")) 
						return 'mkdir in thumbpath failed';
				}
				
				// save thumb
				if ($ext == 'jpg') {
					if (!imagejpeg($tim,"$thumbpath/$thumb_dir1/$thumb_dir2/$fn",75)) 
						return "save thumb failed [$ext]";
				} elseif ($ext == 'png') {
					if (!imagepng($tim,"$thumbpath/$thumb_dir1/$thumb_dir2/$fn",4)) 
						return "save thumb failed [$ext]";
				} elseif($ext == 'gif') {
					if (!imagegif($tim,"$thumbpath/$thumb_dir1/$thumb_dir2/$fn")) 
						return "save thumb failed [$ext]";
				} elseif($ext == 'bmp') {
					if (!imagebmp($tim,"$thumbpath/$thumb_dir1/$thumb_dir2/$fn")) 
						return "save thumb failed [$ext]";
				} else {
					return "save thumb failed [$ext], unknown extension";
				}
			}
		}
		
		// success
		return true;
	}

	function canDelete()
	{
		global $login;
		$login->verify();

		if ($login->userid != $this->nUserId && ($login->admin & ADMIN_USER) != ADMIN_USER)
			return false;

		return 
			sql_value("SELECT COUNT(*) FROM `caches` WHERE `user_id`='&1'", 0, $this->nUserId)
		  + sql_value("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1'", 0, $this->nUserId)
			+ sql_value("SELECT COUNT(*) FROM `cache_logs_archived` WHERE `user_id`='&1'", 0, $this->nUserId)
			+ sql_value("SELECT COUNT(*) FROM `cache_reports` WHERE `userid`='&1'", 0, $this->nUserId)
			== 0;
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
		// all data in depending tables is cleared via trigger 

		$this->reload();

		return true;
	}

	// email bounce processing
	function addEmailProblem($licensemail=false)
	{
		// mailing_problems is a bit-flag field to remember nondelivered, important mailings
		if ($licensemail)
			if (!$this->reUser->setValue('mailing_problems', $this->reUser->getValue('mailing_problems') | 1))
				return false;

		return $this->reUser->setValue('email_problems', $this->getEmailProblems() + 1) &&
		       $this->reUser->setValue('last_email_problem', date('Y-m-d H:i:s')) &&
					 $this->save();
	}

	function getEmailProblems()
	{
		// see also common.inc.php "SELECT `email_problems`"
		return $this->reUser->getValue('email_problems');
	}

	function getDataLicense()
	{
		return $this->reUser->getValue('data_license');
	}

	function getLicenseDeclined()
	{
		return $this->getDataLicense() == NEW_DATA_LICENSE_ACTIVELY_DECLINED ||
		       $this->getDataLicense() == NEW_DATA_LICENSE_PASSIVELY_DECLINED;
	}

	function missedDataLicenseMail()
	{
		return $this->reUser->getValue('mailing_problems') & 1;
	}

	function confirmEmailAddress()
	{
		return $this->reUser->setValue('email_problems', 0) && $this->save();
	}

	function reload()
	{
		$this->reUser->reload();
		$this->reUserStat->reload();
	}
	
	function getGivenRatings()
	{
		// get number of cache ratings for this user
		return sql_value("
							SELECT COUNT(`user_id`)
							FROM `cache_rating`
							WHERE `user_id`='&1'",
						0,
						$this->getUserId());
	}
	
	function getMaxRatings()
	{
		global $opt;
		
		// get number of possible rates
		return floor($this->getStatFound() * $opt['logic']['rating']['percentageOfFounds'] / 100);
	}
	
	function allowRatings()
	{
		// new ratings allowed, if "given ratings" < "max ratings"
		return ($this->getGivenRatings() < $this->getMaxRatings());
	}
	
	function foundsUntilNextRating()
	{
		global $opt;
		
		return ($opt['logic']['rating']['percentageOfFounds'] - ($this->getStatFound() % $opt['logic']['rating']['percentageOfFounds']));
	}
	
	function showStatFounds()
	{
		// wether to show the number of founds on log page
		// TODO: make customisable in user profile, see #241
		return false;
	}
}
?>
