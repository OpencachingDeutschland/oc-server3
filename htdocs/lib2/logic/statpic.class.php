<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');

class statpic
{
	var $nUserId = 0;

	var $reUser;

	function __construct($nNewUserId)
	{
		$this->reUser = new rowEditor('user');
		$this->reUser->addPKInt('user_id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reUser->addString('statpic_text', '', false);
		$this->reUser->addString('statpic_logo', 0, false);

		$this->nUserId = $nNewUserId+0;

		$this->reUser->load($this->nUserId);
	}

	function getStyle()
	{
		return $this->reUser->getValue('statpic_logo');
	}

	function setStyle($value)
	{
		return $this->reUser->setValue('statpic_logo', $value);
	}

	function getText()
	{
		return $this->reUser->getValue('statpic_text');
	}

	function setText($value)
	{
		if ($value != '')
			if (!mb_ereg_match(REGEX_STATPIC_TEXT, $value))
				return false;

		return $this->reUser->setValue('statpic_text', $value);
	}

	function save()
	{
		$retval = $this->reUser->save();
		if ($retval)
			$this->invalidate();

		return $retval;
	}

	// force regeneration of image on next call of ocstats.php
	function invalidate()
	{
		sql("DELETE FROM `user_statpic` WHERE `user_id`='&1'", $this->nUserId);
	}
	
	
	function deleteFile()
	{
		global $opt;
		
		// if data changed - delete statpic of user, if exists - will be recreated on next request
		if (file_exists($opt['rootpath'].'images/statpics/statpic'.$this->nUserId.'.jpg'))
		{
			unlink($opt['rootpath'].'images/statpics/statpic'.$this->nUserId.'.jpg');
		}
	}
}
?>