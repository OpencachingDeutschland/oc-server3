<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Searches for files which are no longer Unicode-encoded
***************************************************************************/

chdir ("../../htdocs");
require('lib2/cli.inc.php');


scan('.', false);

foreach (
	array('api', 'lang', 'lib', 'lib2', 'libse', 'templates2', 'util', 'util2', 'xml')
	as $dir)
{
	scan($dir,true);
}

exit;


function scan($dir, $subdirs)
{
	$hDir = opendir($dir);
	if ($hDir !== false)
	{
		while (($file = readdir($hDir)) !== false)
		{
			$path = $dir . '/' . $file;
			if (is_dir($path) && substr($file,0,1) != '.' && $subdirs)
				scan($path,$subdirs);
			else if (is_file($path))
				if ((substr($file, -4) == '.tpl') || (substr($file, -4) == '.php'))
					test_encoding($path);
		}
		closedir($hDir);
	}
}


function test_encoding($path)
{
	$contents = file_get_contents($path, false, null, 0, 2048);
	$ur = stripos($contents, "Unicode Reminder");
	if ($ur)
		if (mb_trim(mb_substr($contents, $ur+17,2)) != "メモ")
			echo "Bad Unicode Reminder found in $path: ".mb_trim(mb_substr($contents, $ur+17,2))."\n";
}


?>
