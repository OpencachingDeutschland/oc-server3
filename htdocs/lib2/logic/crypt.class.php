<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Cryptographic library for encryption of passwords.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class crypt
{
	static function encryptPassword($password)
	{
		// Calls the password encryption chained
		$pwmd5 = crypt::firstStagePasswordEncryption($password);
		return crypt::secondStagePasswordEncryption($pwmd5);
	}

	static function firstStagePasswordEncryption($password)
	{
		return md5($password);
	}

	static function secondStagePasswordEncryption($password)
	{
		global $opt;
		if ($opt['logic']['password_hash'])
		{
			return hash_hmac('sha512', $password, $opt['logic']['password_salt']);
		}
		return $password;
	}
}
?>