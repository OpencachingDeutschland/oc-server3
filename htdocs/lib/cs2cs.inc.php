<?php

	// Unicode Reminder メモ

define("CS2CS", "cs2cs");


function cs2cs_core($lat, $lon, $to) {
 
  $descriptorspec = array(
			  0 => array("pipe", "r"),     // stdin is a pipe that the child will read from
			  1 => array("pipe", "w"),     // stdout is a pipe that the child will write to
			  2 => array("pipe", "w")      // stderr is a pipe that the child will write to
			  );

  if (mb_eregi('^[a-z0-9_ ,\+\-=]*$', $to) == 0) {
    die("invalid arguments in command: " . $to ."\n");
  }

  $command = CS2CS . " +proj=latlong +datum=WGS84 +to " . $to; 

  $process = proc_open($command, $descriptorspec, $pipes);

  if (is_resource($process)) {

    fwrite($pipes[0], $lon . " " . $lat);
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

    if ($stderr) {
    	die("proc_open() failed:<br>command='$command'<br>stderr='" . $stderr . "'");
    }
 
    proc_close($process);

    return explode_multi(mb_trim($stdout), "\t\n ");

  } else {
    die("proc_open() failed, command=$command\n");
  }
}

function cs2cs_utm($lat, $lon) {

   // get UTM letter
    if ( $lat <= 84.0 && $lat >= 72.0 )
        $utmLetter = 'X';
    else if ( $lat < 72.0 && $lat >= 64.0 )
        $utmLetter = 'W';
    else if ( $lat < 64.0 && $lat >= 56.0 )
        $utmLetter = 'V';
    else if ( $lat < 56.0 && $lat >= 48.0 )
        $utmLetter = 'U';
    else if ( $lat < 48.0 && $lat >= 40.0 )
        $utmLetter = 'T';
    else if ( $lat < 40.0 && $lat >= 32.0 )
        $utmLetter = 'S';
    else if ( $lat < 32.0 && $lat >= 24.0 )
        $utmLetter = 'R';
    else if ( $lat < 24.0 && $lat >= 16.0 )
        $utmLetter = 'Q';
    else if ( $lat < 16.0 && $lat >= 8.0 )
        $utmLetter = 'P';
    else if ( $lat < 8.0 && $lat >= 0.0 )
        $utmLetter = 'N';
    else if ( $lat < 0.0 && $lat >= -8.0 )
        $utmLetter = 'M';
    else if ( $lat < -8.0 && $lat >= -16.0 )
        $utmLetter = 'L';
    else if ( $lat < -16.0 && $lat >= -24.0 )
        $utmLetter = 'K';
    else if ( $lat < -24.0 && $lat >= -32.0 )
        $utmLetter = 'J';
    else if ( $lat < -32.0 && $lat >= -40.0 )
        $utmLetter = 'H';
    else if ( $lat < -40.0 && $lat >= -48.0 )
        $utmLetter = 'G';
    else if ( $lat < -48.0 && $lat >= -56.0 )
        $utmLetter = 'F';
    else if ( $lat < -56.0 && $lat >= -64.0 )
        $utmLetter = 'E';
    else if ( $lat < -64.0 && $lat >= -72.0 )
        $utmLetter = 'D';
    else if ( $lat < -72.0 && $lat >= -80.0 )
        $utmLetter = 'C';
    else
        $utmLetter = 'Z'; //returns 'Z' if the lat is outside the UTM limits of 84N to 80S

    $zone = (int) ( ( $lon + 180 ) / 6 ) + 1;

    if ( $lat >= 56.0 && $lat < 64.0 && $lon >= 3.0 && $lon < 12.0 ) { $zone = 32; }

    // Special zones for Svalbard.
    if ($lat >= 72.0 && $lat < 84.0 )
    {
        if ( $lon >= 0.0 && $lon < 9.0 )
            $zone = 31;
        else if ( $lon >= 9.0 && $lon < 21.0 )
            $zone = 33;
        else if ( $lon >= 21.0 && $lon < 33.0 )
            $zone = 35;
        else if ( $lon >= 33.0 && $lon < 42.0 )
            $zone = 37;
    }

    $cs2csresult = cs2cs_core($lat, $lon, "+proj=utm +datum=WGS84 +zone=$zone");

    return array_merge(Array($zone, $utmLetter), $cs2csresult);
}

function cs2cs_gk($lat, $lon) {
 
  $zone = round($lon/3);
  $falseeasting = $zone * 1000000 + 500000;

  $cs2csresult = cs2cs_core($lat, $lon, "+proj=tmerc +ellps=bessel +lat_0=0 +lon_0=".($zone*3)." +x_0=".$falseeasting." +towgs84=606,23,413 ");
  
  return $cs2csresult;

}


?>
