<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$cli = new cli();

class cli
{
	function out($str)
	{
		echo $str . "\n";
	}

	function debug($str)
	{
		global $opt;
		if (($opt['debug'] & DEBUG_CLI) == DEBUG_CLI)
			echo 'DEBUG: ' . $str . "\n";
	}

	function warn($str)
	{
		echo 'WARN: ' . $str . "\n";
	}

	function error($str)
	{
		echo 'ERROR: ' . $str . "\n";
	}

	function fatal($str)
	{
		echo 'FATAL: ' . $str . "\n";
		exit;
	}
}
?>