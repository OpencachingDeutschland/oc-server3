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

	// do all output in HTML format
	$opt['gui'] = GUI_HTML;

	// include the main library
	require($opt['rootpath'] . 'lib2/common.inc.php');

	// https protection?
	if ($opt['page']['allowhttps'] == false)
	{
		if (isset($_SERVER['HTTPS']))
			$tpl->redirect($opt['page']['absolute_url']);
	}
?>