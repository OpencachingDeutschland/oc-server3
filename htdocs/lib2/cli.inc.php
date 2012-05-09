<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  This module is included by each site with HTML-output and contains 
 *  functions that are specific to HTML-output. common.inc.php is included
 *  and will do the setup.
 *
 *  If you include this script from any subdir, you have to set the 
 *  variable $opt['rootpath'], so that it points (relative or absolute)
 *  to the root.
 ***************************************************************************/

	// setup rootpath
	if (!isset($opt['rootpath'])) $opt['rootpath'] = './';

	// chicken-egg problem ...
	require($opt['rootpath'] . 'lib2/const.inc.php');

	// do all output in text format
	$opt['gui'] = GUI_TEXT;

	// include the main library
	require($opt['rootpath'] . 'lib2/common.inc.php');
	require_once($opt['rootpath'] . 'lib2/cli.class.php');

	if (($opt['debug'] & DEBUG_OUTOFSERVICE) == DEBUG_OUTOFSERVICE)
	{
		$cli->debug('exit because DEBUG_OUTOFSERVICE is set');
		exit;
	}
?>