<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Workaround for OKAPI issue #246
 ***************************************************************************/

checkJob(new okapi_cleanup());

class okapi_cleanup
{
	var $name = 'okapi_cleanup';
	var $interval = 3600;

	function run()
	{
		global $opt;

		$files = glob($opt['okapi']['var_dir'] . '/garmin*.zip');
		foreach ($files as $file)
		{
			// delete old download files after 24 hours; this large interval filters out any
			// timezone mismatches in file sysytems (e.g. on unconventional development
			// environments)
			if (is_file($file) && (time() - filemtime($file)) > 24 * 3600)
				unlink($file);
		}
	}
}

?>