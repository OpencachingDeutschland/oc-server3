<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  !!! IMPORTANT !!!
 *
 *  Only use this file on development systems!
 *  NOT for productive use!
 *
 *  !!! IMPORTANT !!!
 *
 *  Default settings for all options in sqlroot.inc.php
 *  Do not modify this file - use settings.inc.php!
 ***************************************************************************/

	if ($opt['debug'] == DEBUG_NO)
		die('sqlroot.inc.php cannot be included on productive systems, set $opt[\'debug\'] != DEBUG_NO');

	/* creditials for db-root
	 * needs all privileges to all oc-databases
	 */
 	$opt['sqlroot']['username'] = 'root';
 	$opt['sqlroot']['password'] = 'secret';
?>