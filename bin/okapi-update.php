<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	/*
	 * run okapi database update
	 * needs 'short_open_tag = Off' in php.ini
	 *
	 * You should normally NOT call this script directly, but via dbupdate.php
	 * (or something similar on a production system). This ensures that
	 * everything takes place in the right order.
	 */

	okapi_update();


	function okapi_update()
	{
		$GLOBALS['rootpath'] = dirname(__FILE__) . '/../htdocs/';
		require_once($GLOBALS['rootpath']."okapi/facade.php");
		okapi\Facade::database_update();
	}

?>