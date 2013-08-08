<?php
 /***************************************************************************
 *  For license information see doc/license.txt
 *
 *	This script converts all md5-passwords to salted hash passwords.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

global $opt;
$opt['rootpath'] = '../htdocs/';
require($opt['rootpath'] . 'lib2/web.inc.php');
require($opt['rootpath'] . 'lib2/logic/crypt.class.php');

if (!isset($opt['logic']['password_salt']) || strlen($opt['logic']['password_salt']) < 32)
{
	echo "Warning!\nPassword Salt not set or too short!\n\n";
	return;
}
if (!$opt['logic']['password_hash'])
{
	echo "Warning!\nHashed Passwords not enabled!\n\n";
	return;
}

$rs = sql("SELECT * FROM user where password is not null");
while ($r = sql_fetch_array($rs))
{
	$password = $r['password'];
	if (strlen($password) == 128)
	{
		echo "Password seems to be already converted, ommit this password\n";
		continue;
	}
	if (strlen($password) < 32)
	{
		$password = crypt::firstStagePasswordEncryption($password);
	}
	$pwhash = crypt::secondStagePasswordEncryption($password);

	sql("UPDATE `user` SET `password`='&1' WHERE `user_id`='&2'", $pwhash, $r['user_id']);
}

mysql_free_result($rs);

echo "Update of passwords finished.\n";

?>