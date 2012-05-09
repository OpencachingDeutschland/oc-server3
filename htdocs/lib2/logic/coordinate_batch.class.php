<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

/* $opt['bin']['cs2cs'] must be set!
 */

class coordinate_batch
{
	var $pipes = array();
	var $process;

	function writeGK($x, $y)
	{
		fwrite($this->pipes[0], $x . " " . $y . "\n");
	}

	function analyseOutput()
	{
		$retval = array();

		fclose($this->pipes[0]);

		$stdout = stream_get_contents($this->pipes[1]);
		fclose($this->pipes[1]);

		$stderr = stream_get_contents($this->pipes[2]);
		fclose($this->pipes[2]);

		proc_close($this->process);

		if ($stderr != '')
			die('Stderr is not empty!' . "\n");

		$output = explode("\n", $stdout);
		for ($n = 0; $n < count($output); $n++)
			if ($output[$n] != '')
				$retval[] = $this->parseOutputLine($output[$n]);

		return $retval;
	}
	
	function parseOutputLine($str)
	{
		$nLon = 0;
		$nLat = 0;

		$parts = explode_multi(mb_trim($str), "\t\n ");
		if (count($parts) == 3)
		{
			if (strpos($parts[0], '\'') === false)
			{
				preg_match('/^(\d+)dE$/', $parts[0], $aLon);
				$nLon = $aLon[1];
			}
			else if (strpos($parts[0], '"') === false)
			{
				preg_match('/^(\d+)d(\d+)\'E$/', $parts[0], $aLon);
				$nLon = $aLon[1] + ($aLon[2]/60);
			}
			else
			{
				preg_match('/^(\d+)d(\d+)\'([\d\.]+)"E$/', $parts[0], $aLon);
				$nLon = $aLon[1] + ($aLon[2]/60) + ($aLon[3]/3600);
			}

			if (strpos($parts[1], '\'') === false)
			{
				preg_match('/^(\d+)dN$/', $parts[1], $aLat);
				$nLat = $aLat[1];
			}
			else if (strpos($parts[1], '"') === false)
			{
				preg_match('/^(\d+)d(\d+)\'N$/', $parts[1], $aLat);
				$nLat = $aLat[1] + ($aLat[2]/60);
			}
			else
			{
				preg_match('/^(\d+)d(\d+)\'([\d+\.]+)"N$/', $parts[1], $aLat);
				$nLat = $aLat[1] + ($aLat[2]/60) + ($aLat[3]/3600);
			}
		}

		$coord = array('lon' => $nLon, 'lat' => $nLat);
		return $coord;
	}

	function openGK()
	{
		$rechts = 3515222;
		$zone = round(($rechts - 500000) / 1000000);
		$falseeasting = $zone * 1000000 + 500000;
		$this->open("+proj=tmerc +lat_0=0 +lon_0=" . ($zone*3) . " +k=1.000000 +x_0=" . $falseeasting . " +y_0=0 +ellps=bessel +towgs84=606,23,413 +units=m +no_defs +to +proj=latlong +datum=WGS84");
	}

	function open($command)
	{
		global $opt;

		$descriptorspec = array(
														0 => array("pipe", "r"),     // stdin is a pipe that the child will read from
														1 => array("pipe", "w"),     // stdout is a pipe that the child will write to
														2 => array("pipe", "w")      // stderr is a pipe that the child will write to
														);

		if (mb_eregi('^[a-z0-9_ ,\+\-=\.]*$', $command) == 0)
			die("invalid arguments in command: " . $command ."\n");

		$command = $opt['bin']['cs2cs'] . " " . $command;

		$this->process = proc_open($command, $descriptorspec, $this->pipes);

		if (!is_resource($this->process))
			die("proc_open() failed, command=$command\n");
	}
}
?>
