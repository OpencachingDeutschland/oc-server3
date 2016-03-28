<?php
 /***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Fix some common coding style issues. This script may be run any time
 *  to check and clean up the current code.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$exclude = array(
	'htdocs/cache',
	'htdocs/cache2',
	'htdocs/lib2/HTMLPurifier',
	'htdocs/lib2/smarty',
	'htdocs/okapi'
);

chdir(__DIR__ . '/../..');

list($modified_files, $modified_lines) = cleanup('.', $exclude);
echo $modified_lines . " lines in " . $modified_files . " files have been cleaned up\n";


function cleanup($path, $exclude)
{
	$modified_files = 0;
	$modified_lines = 0;

	if (!in_array(substr($path, 2), $exclude))
	{
		$files = glob($path . '/*.php');
		foreach ($files as $filepath)
		{
			$modified = false;
			$lines = file($filepath);

			# Remove all trailing whitespaces, strip CRs and make sure
			# that the the last line ends on "\n".
			foreach ($lines as &$line) {
				$trimmed_line = trim($line, " \t\r\n");
				if ($trimmed_line != '' && $trimmed_line != '*') {
					$old_line = $line;
					$line = rtrim($line, " \t\r\n") . "\n";
					if ($line != $old_line)	{
						$modified = true;
						++$modified_lines;
					}
				}
			}

			# remove PHP close tags and empty lines from end of file
			$l = count($lines) - 1;
			while ($l > 0) {
				$trimmed_line = trim($lines[$l]);
				if ($trimmed_line == '?>' || $trimmed_line == '') {
					unset($lines[$l]);
					$modified = true;
					++$modified_lines;
				}
				else
					break;
				--$l;
			}

			if ($modified) {
				echo substr($filepath, 2) . "\n";
				file_put_contents($filepath, implode('', $lines));
				++$modified_files;
			}
		}

		$dirs = glob($path . '/*', GLOB_ONLYDIR);
		foreach ($dirs as $dir) {
			if ($dir != '.' && $dir != '..') {
				list($mf, $ml) = cleanup($dir, $exclude);
				$modified_files += $mf;
				$modified_lines += $ml;
			}
		}
	}

	return array($modified_files, $modified_lines);
}
