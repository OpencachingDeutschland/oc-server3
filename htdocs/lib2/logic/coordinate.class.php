<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

/* $opt['bin']['cs2cs'] must be set!
 */

class coordinate
{
	var $nLat = 0;
	var $nLon = 0;

	function __construct($nNewLat, $nNewLon)
	{
		$this->nLat = $nNewLat;
		$this->nLon = $nNewLon;
	}

	static function fromGK($rechts, $hoch)
	{
		//$zone = round($this->nLon/3);
		//$falseeasting = $zone * 1000000 + 500000;

		$zone = round(($rechts - 500000) / 1000000);
		$falseeasting = $zone * 1000000 + 500000;

		$cs2csresult = self::getCoreCommand($rechts, $hoch, "+proj=tmerc +lat_0=0 +lon_0=" . ($zone*3) . " +k=1.000000 +x_0=" . $falseeasting . " +y_0=0 +ellps=bessel +towgs84=606,23,413 +units=m +no_defs +to +proj=latlong +datum=WGS84");
		//$cs2csresult = self::getCoreCommand($rechts, $hoch, "+proj=tmerc +lat_0=0 +lon_0=9 +k=1.000000 +x_0=3500000 +y_0=0 +ellps=bessel +towgs84=591.28,81.35,396.39,1.477,-0.0736,-1.458,9.82 +units=m +no_defs +to +proj=latlong +datum=WGS84");

		preg_match('/^(\d+)d(\d+)\'(\d+\.\d+)"E$/', $cs2csresult[0], $aLon);
		$lon = $aLon[1] + ($aLon[2]/60) + ($aLon[3]/3600);

		preg_match('/^(\d+)d(\d+)\'(\d+\.\d+)"N$/', $cs2csresult[1], $aLat);
		$lat = $aLat[1] + ($aLat[2]/60) + ($aLat[3]/3600);

		return new coordinate($lat, $lon);
	}

	/* get-Functions return array([lat] => string, [lon] => string)
	 */

	function getFloat()
	{
		return array('lat' => $this->nLat, 'lon' => $this->nLon);
	}

	// d.ddddd°
	function getDecimal()
	{
		if ($this->nLat < 0)
			$sLat = 'S ' . sprintf('%08.5f', -$this->nLat) . '°';
		else
			$sLat = 'N ' . sprintf('%08.5f', $this->nLat) . '°';

		if ($this->nLon < 0)
			$sLon = 'W ' . sprintf('%09.5f', -$this->nLon) . '°';
		else
			$sLon = 'E ' . sprintf('%09.5f', $this->nLon) . '°';

		return array('lat' => $sLat, 'lon' => $sLon);
	}

	// d° mm.mmm
	function getDecimalMinutes()
	{
		$nLat = $this->nLat;
		$bLatN = ($nLat < 0) ? false : true;
		if (!$bLatN) $nLat = -$nLat;
		$nLatDeg = floor($nLat);
		$nLatMin = ($nLat - $nLatDeg) * 60;
		if ($bLatN)
			$sLat = 'N ' . sprintf("%02d", $nLatDeg) . '° ' . sprintf("%06.3f", $nLatMin) . '\'';
		else
			$sLat = 'S ' . sprintf("%02d", $nLatDeg) . '° ' . sprintf("%06.3f", $nLatMin) . '\'';

		$nLon = $this->nLon;
		$bLonE = ($nLon < 0) ? false : true;
		if (!$bLonE) $nLon = -$nLon;
		$nLonDeg = floor($nLon);
		$nLonMin = ($nLon - $nLonDeg) * 60;
		if ($bLonE)
			$sLon = 'E ' . sprintf("%03d", $nLonDeg) . '° ' . sprintf("%06.3f", $nLonMin) . '\'';
		else
			$sLon = 'W ' . sprintf("%03d", $nLonDeg) . '° ' . sprintf("%06.3f", $nLonMin) . '\'';

		return array('lat' => $sLat, 'lon' => $sLon);
	}

	// d° mm ss
	function getDecimalMinutesSeconds()
	{
		$nLat = $this->nLat;
		$bLatN = ($nLat < 0) ? false : true;
		if (!$bLatN) $nLat = -$nLat;
		$nLatDeg = floor($nLat);
		$nLatMin = ($nLat - $nLatDeg) * 60;
		$nLatSec = $nLatMin - floor($nLatMin);
		$nLatMin = ($nLatMin - $nLatSec);
		$nLatSec = $nLatSec * 60;
		if ($bLatN)
			$sLat = 'N ' . sprintf("%02d", $nLatDeg) . '° ' . sprintf("%02d", $nLatMin) . '\' ' . sprintf("%02d", $nLatSec) . '\'\'';
		else
			$sLat = 'S ' . sprintf("%02d", $nLatDeg) . '° ' . sprintf("%02d", $nLatMin) . '\' ' . sprintf("%02d", $nLatSec) . '\'\'';

		$nLon = $this->nLon;
		$bLonE = ($nLon < 0) ? false : true;
		if (!$bLonE) $nLon = -$nLon;
		$nLonDeg = floor($nLon);
		$nLonMin = ($nLon - $nLonDeg) * 60;
		$nLonSec = $nLonMin - floor($nLonMin);
		$nLonMin = ($nLonMin - $nLonSec);
		$nLonSec = $nLonSec * 60;
		if ($bLonE)
			$sLon = 'E ' . sprintf("%03d", $nLonDeg) . '° ' . sprintf("%02d", $nLonMin) . '\' ' . sprintf("%02d", $nLonSec) . '\'\'';
		else
			$sLon = 'W ' . sprintf("%03d", $nLonDeg) . '° ' . sprintf("%02d", $nLonMin) . '\' ' . sprintf("%02d", $nLonSec) . '\'\'';

		return array('lat' => $sLat, 'lon' => $sLon);
	}

	// array(zone, letter, north, east)
	function getUTM()
	{
		// get UTM letter
		if ( $this->nLat <= 84.0 && $this->nLat >= 72.0 )
			$utmLetter = 'X';
		else if ( $this->nLat < 72.0 && $this->nLat >= 64.0 )
			$utmLetter = 'W';
		else if ( $this->nLat < 64.0 && $this->nLat >= 56.0 )
			$utmLetter = 'V';
		else if ( $this->nLat < 56.0 && $this->nLat >= 48.0 )
			$utmLetter = 'U';
		else if ( $this->nLat < 48.0 && $this->nLat >= 40.0 )
			$utmLetter = 'T';
		else if ( $this->nLat < 40.0 && $this->nLat >= 32.0 )
			$utmLetter = 'S';
		else if ( $this->nLat < 32.0 && $this->nLat >= 24.0 )
			$utmLetter = 'R';
		else if ( $this->nLat < 24.0 && $this->nLat >= 16.0 )
			$utmLetter = 'Q';
		else if ( $this->nLat < 16.0 && $this->nLat >= 8.0 )
			$utmLetter = 'P';
		else if ( $this->nLat < 8.0 && $this->nLat >= 0.0 )
			$utmLetter = 'N';
		else if ( $this->nLat < 0.0 && $this->nLat >= -8.0 )
			$utmLetter = 'M';
		else if ( $this->nLat < -8.0 && $this->nLat >= -16.0 )
			$utmLetter = 'L';
		else if ( $this->nLat < -16.0 && $this->nLat >= -24.0 )
			$utmLetter = 'K';
		else if ( $this->nLat < -24.0 && $this->nLat >= -32.0 )
			$utmLetter = 'J';
		else if ( $this->nLat < -32.0 && $this->nLat >= -40.0 )
			$utmLetter = 'H';
		else if ( $this->nLat < -40.0 && $this->nLat >= -48.0 )
			$utmLetter = 'G';
		else if ( $this->nLat < -48.0 && $this->nLat >= -56.0 )
			$utmLetter = 'F';
		else if ( $this->nLat < -56.0 && $this->nLat >= -64.0 )
			$utmLetter = 'E';
		else if ( $this->nLat < -64.0 && $this->nLat >= -72.0 )
			$utmLetter = 'D';
		else if ( $this->nLat < -72.0 && $this->nLat >= -80.0 )
			$utmLetter = 'C';
		else
			$utmLetter = 'Z'; //returns 'Z' if the lat is outside the UTM limits of 84N to 80S

		$zone = (int) ( ( $this->nLon + 180 ) / 6 ) + 1;

		if ( $this->nLat >= 56.0 && $this->nLat < 64.0 && $this->nLon >= 3.0 && $this->nLon < 12.0 ) $zone = 32;

		// Special zones for Svalbard.
		if ($this->nLat >= 72.0 && $this->nLat < 84.0 )
		{
			if ( $this->nLon >= 0.0 && $this->nLon < 9.0 )
				$zone = 31;
			else if ( $this->nLon >= 9.0 && $this->nLon < 21.0 )
				$zone = 33;
			else if ( $this->nLon >= 21.0 && $this->nLon < 33.0 )
				$zone = 35;
			else if ( $this->nLon >= 33.0 && $this->nLon < 42.0 )
				$zone = 37;
		}

		$cs2csresult = $this->getCore("+proj=utm +datum=WGS84 +zone=$zone");

		return Array('zone' => $zone, 'letter' => $utmLetter, 'north' => 'N ' . floor($cs2csresult[1]), 'east' => 'E ' . floor($cs2csresult[0]));
	}

	// return string
	function getGK()
	{
		$zone = round($this->nLon/3);
		$falseeasting = $zone * 1000000 + 500000;

		$cs2csresult = $this->getCore("+proj=tmerc +ellps=bessel +lat_0=0 +lon_0=".($zone*3)." +x_0=".$falseeasting." +towgs84=606,23,413 ");

		return 'R ' . floor($cs2csresult[0]) . ' H ' . floor($cs2csresult[1]);
	}

	// return string
	function getRD()
	{
		$cs2csresult = $this->getCore("+proj=sterea +lat_0=52.15616055555555 +lon_0=5.38763888888889 +k=0.9999079 +x_0=155000 +y_0=463000 +towgs84=565.040,49.910,465.840,-0.40939,0.35971,-1.86849,4.0772 +ellps=bessel ");
		return 'X ' . floor($cs2csresult[0]) . ' Y ' . floor($cs2csresult[1]);
	}

	// returns string
	function getQTH()
	{
		$lon = $this->nLon;
		$lat = $this->nLat;

		$lon += 180;
		$l[0] = floor($lon/20);
		$lon -= 20*$l[0];
		$l[2] = floor($lon/2);
		$lon -= 2 *$l[2];
		$l[4] = floor($lon*60/5);

		$lat += 90;
		$l[1] = floor($lat/10);
		$lat -= 10*$l[1];
		$l[3] = floor($lat);
		$lat -= $l[3];
		$l[5] = floor($lat*120/5);

		return sprintf("%c%c%c%c%c%c", $l[0]+65, $l[1]+65, $l[2]+48, $l[3]+48, $l[4]+65, $l[5]+65);
	}

	// return string
	function getSwissGrid()
	{
		$nLat = $this->nLat * 3600;
		$nLon = $this->nLon * 3600;

		// Quelle: http://www.swisstopo.admin.ch/internet/swisstopo/de/home/apps/calc.html
		// Hilfsgrössen
		$b = ($nLat - 169028.66) / 10000.0;
		$l = ($nLon - 26782.5) / 10000.0;

		// Nord x
		$x =   200147.07 + 308807.95 * $b + 3745.25 * $l * $l +  76.63 * $b * $b + 119.79 * $b * $b * $b - 194.56 * $b * $l * $l;
		$x = floor($x);

		// Ost y
		$y =   600072.37 + 211455.93 * $l - 10938.51 * $l * $b - 0.36 * $l * $b * $b - 44.54 * $l * $l * $l;
		$y = floor($y);

		// Namen: "CH1903", "Schweizer Landeskoordinaten" oder "Swiss Grid"
		$swissgrid = "$y / $x";
		// Karten Links
		$mapplus = "<a href=\"http://www.mapplus.ch/frame.php?map=&x=$y&y=$x&zl=13\" target=\"_blank\">MapPlus</a>";
		$mapsearch = "<a href=\"http://map.search.ch/$y,$x\" target=\"_blank\">map.search.ch</a>";
		
		return array('coord' => $swissgrid, $mapplus, $mapsearch);
	}

	function getCore($to)
	{
		return $this->getCoreCommand($this->nLon, $this->nLat, " +proj=latlong +datum=WGS84 +to " . $to);
	}

	static function getCoreCommand($x, $y, $command)
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

		$process = proc_open($command, $descriptorspec, $pipes);

		if (is_resource($process))
		{
			fwrite($pipes[0], $x . " " . $y);
			fclose($pipes[0]);

			$stdout = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$stderr = stream_get_contents($pipes[2]);
			fclose($pipes[2]);
	 
			// 
			// $procstat = proc_get_status($process);
			// 
			// neither proc_close nor proc_get_status return reasonable results with PHP5 and linux 2.6.11,
			// see http://bugs.php.net/bug.php?id=32533
			//
			// as temporary (?) workaround, check stderr output.
			// (Vinnie, 2006-02-09)

			if ($stderr)
    		die("proc_open() failed:<br>command='$command'<br>stderr='" . $stderr . "'");

			proc_close($process);

			return explode_multi(mb_trim($stdout), "\t\n ");
		}
		else
			die("proc_open() failed, command=$command\n");
	}

	static function parseRequestLat($name)
	{
		if (!isset($_REQUEST[$name . 'NS']) || !isset($_REQUEST[$name . 'Lat']) || !isset($_REQUEST[$name . 'LatMin']))
			return false;

		$coordNS = $_REQUEST[$name . 'NS'];
		$coordLat = $_REQUEST[$name . 'Lat']+0;
		$coordLatMin = str_replace(',', '.', $_REQUEST[$name . 'LatMin'])+0;

		$lat = $coordLat + $coordLatMin/60;
		if ($coordNS == 'S')
			$lat = -$lat;

		return $lat;
	}

	static function parseRequestLon($name)
	{
		if (!isset($_REQUEST[$name . 'EW']) || !isset($_REQUEST[$name . 'Lon']) || !isset($_REQUEST[$name . 'LonMin']))
			return false;

		$coordEW = $_REQUEST[$name . 'EW'];
		$coordLon = $_REQUEST[$name . 'Lon']+0;
		$coordLonMin = str_replace(',', '.', $_REQUEST[$name . 'LonMin'])+0;

		$lon = $coordLon + $coordLonMin/60;
		if ($coordEW == 'W')
			$lon = -$lon;

		return $lon;
	}
}
?>