<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class coordinate
{
	var $nLat = 0;
	var $nLon = 0;

	function __construct($nNewLat, $nNewLon)
	{
		$this->nLat = $nNewLat;
		$this->nLon = $nNewLon;
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
		// Ocprop: ([N|S].*?)&#039;
		$nLat = $this->nLat;
		$bLatN = ($nLat < 0) ? false : true;
		if (!$bLatN) $nLat = -$nLat;
		$nLatDeg = floor($nLat);
		$nLatMin = ($nLat - $nLatDeg) * 60;
		if ($bLatN)
			$sLat = 'N ' . sprintf("%02d", $nLatDeg) . '° ' . sprintf("%06.3f", $nLatMin) . '\'';
		else
			$sLat = 'S ' . sprintf("%02d", $nLatDeg) . '° ' . sprintf("%06.3f", $nLatMin) . '\'';

		// Ocprop: ([E|W].*?)&#039;
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
		/* Copyright (c) 2006, HELMUT H. HEIMEIER
		   Permission is hereby granted, free of charge, to any person obtaining a
		   copy of this software and associated documentation files (the "Software"),
		   to deal in the Software without restriction, including without limitation
		   the rights to use, copy, modify, merge, publish, distribute, sublicense,
		   and/or sell copies of the Software, and to permit persons to whom the
		   Software is furnished to do so, subject to the following conditions:
		   The above copyright notice and this permission notice shall be included
		   in all copies or substantial portions of the Software.*/

		/* Die Funktion wandelt geographische Koordinaten in UTM Koordinaten
		   um. Geographische Länge lw und Breite bw müssen im WGS84 Datum
		   gegeben sein. Berechnet werden UTM Zone, Ostwert ew und Nordwert nw.*/

		//Geographische Länge lw und Breite bw im WGS84 Datum
		if ($this->nLon == 0 || $this->nLat == 0) {
			return Array('zone' => "", 'letter' => "", 'north' => 'N ' . 0, 'east' => 'E ' . 0);
		}
		if ($this->nLon <= -180 || $this->nLon > 180 || $this->nLat <= -80 || $this->nLat >= 84) {
			// Werte nicht im Bereich des UTM Systems -180° <= nLon < +180°, -80° < nLat < 84° N
			return array("", "", 0, 0);
		}
		$lw = doubleval($this->nLon);
		$bw = doubleval($this->nLat);

		//WGS84 Datum
		//Große Halbachse a und Abplattung f
		$a = 6378137.000;
		$f = 3.35281068e-3;
		$b_sel = 'CDEFGHJKLMNPQRSTUVWXX';

		//Polkrümmungshalbmesser c
		$c = $a / (1 - $f);

		//Quadrat der zweiten numerischen Exzentrizität
		$ex2 = (2 * $f - $f * $f) / ((1 - $f) * (1 - $f));
		$ex4 = $ex2 * $ex2;
		$ex6 = $ex4 * $ex2;
		$ex8 = $ex4 * $ex4;

		//Koeffizienten zur Berechnung der Meridianbogenlänge
		$e0 = $c * (pi() / 180) * (1 - 3 * $ex2 / 4 + 45 * $ex4 / 64 - 175 * $ex6 / 256 + 11025 * $ex8 / 16384);
		$e2 = $c * (-3 * $ex2 / 8 + 15 * $ex4 / 32 - 525 * $ex6 / 1024 + 2205 * $ex8 / 4096);
		$e4 = $c * (15 * $ex4 / 256 - 105 * $ex6 / 1024 + 2205 * $ex8 / 16384);
		$e6 = $c * (-35 * $ex6 / 3072 + 315 * $ex8 / 12288);

		//Längenzone lz und Breitenzone (Band) bz
		$lzn = intval(($lw + 180) / 6) + 1;

		if ($bw >= 56.0 && $bw < 64.0 && $lw >= 3.0 && $lw < 12.0) $lzn = 32;

		// Special zones for Svalbard.
		if ($bw >= 72.0 && $bw < 84.0) {
			if ($lw >= 0.0 && $lw < 9.0)
				$lzn = 31;
			else if ($lw >= 9.0 && $lw < 21.0)
				$lzn = 33;
			else if ($lw >= 21.0 && $lw < 33.0)
				$lzn = 35;
			else if ($lw >= 33.0 && $lw < 42.0)
				$lzn = 37;
		}

		$lz = "$lzn";
		if ($lzn < 10) $lz = "0".$lzn;
		$bd = intval(1 + ($bw + 80) / 8);
		$bz = substr($b_sel, $bd - 1, 1);

		//Geographische Breite in Radianten br
		$br = $bw * pi() / 180;

		$tan1 = tan($br);
		$tan2 = $tan1 * $tan1;
		$tan4 = $tan2 * $tan2;

		$cos1 = cos($br);
		$cos2 = $cos1 * $cos1;
		$cos4 = $cos2 * $cos2;
		$cos3 = $cos2 * $cos1;
		$cos5 = $cos4 * $cos1;

		$etasq = $ex2 * $cos2;

		//Querkrümmungshalbmesser nd
		$nd = $c / sqrt(1 + $etasq);

		//Meridianbogenlänge g aus gegebener geographischer Breite bw
		$g = ($e0 * $bw) + ($e2 * sin(2 * $br)) + ($e4 * sin(4 * $br)) + ($e6 * sin(6 * $br));

		//Längendifferenz dl zum Bezugsmeridian lh
		$lh = ($lzn - 30) * 6 - 3;
		$dl = ($lw - $lh) * pi() / 180;
		$dl2 = $dl * $dl;
		$dl4 = $dl2 * $dl2;
		$dl3 = $dl2 * $dl;
		$dl5 = $dl4 * $dl;

		//Maßstabsfaktor auf dem Bezugsmeridian bei UTM Koordinaten m = 0.9996
		//Nordwert nw und Ostwert ew als Funktion von geographischer Breite und Länge

		if ($bw < 0) {
			$nw = 10e6 + 0.9996 * ($g + $nd * $cos2 * $tan1 * $dl2 / 2 +
					$nd * $cos4 * $tan1 * (5 - $tan2 + 9 * $etasq) * $dl4 / 24);
		} else {
			$nw = 0.9996 * ($g + $nd * $cos2 * $tan1 * $dl2 / 2 +
					$nd * $cos4 * $tan1 * (5 - $tan2 + 9 * $etasq) * $dl4 / 24);
		}
		$ew = 0.9996 * ($nd * $cos1 * $dl + $nd * $cos3 * (1 - $tan2 + $etasq) * $dl3 / 6 +
				$nd * $cos5 * (5 - 18 * $tan2 + $tan4) * $dl5 / 120) + 500000;

		$nk = $nw - intval($nw);
		if ($nk < 0.5) $nw = "" . intval($nw);
		else $nw = "" . (intval($nw) + 1);

		while (strlen($nw) < 7) {
			$nw = "0" . $nw;
		}

		$nk = $ew - intval($ew);
		if ($nk < 0.5) $ew = "0" . intval($ew);
		else $ew = "0" . intval($ew + 1);

		return Array('zone' => $lz, 'letter' => $bz, 'north' => 'N ' . floor($nw), 'east' => 'E ' . floor($ew));
	}

	// return string
	function getGK()
	{
		$pdResult = $this->wgs2pot($this->nLat, $this->nLon);
		$result = $this->geo2gk($pdResult[1], $pdResult[0]);
		return 'R ' . floor($result[0]) . ' H ' . floor($result[1]);
	}

	function wgs2pot($bw, $lw)
	{
		/* Copyright (c) 2006, HELMUT H. HEIMEIER
		   Permission is hereby granted, free of charge, to any person obtaining a
		   copy of this software and associated documentation files (the "Software"),
		   to deal in the Software without restriction, including without limitation
		   the rights to use, copy, modify, merge, publish, distribute, sublicense,
		   and/or sell copies of the Software, and to permit persons to whom the
		   Software is furnished to do so, subject to the following conditions:
		   The above copyright notice and this permission notice shall be included
		   in all copies or substantial portions of the Software.*/

		/* Die Funktion verschiebt das Kartenbezugssystem (map datum) vom
		   WGS84 Datum (World Geodetic System 84) zum in Deutschland
		   gebräuchlichen Potsdam-Datum. Geographische Länge lw und Breite
		   bw gemessen in grad auf dem WGS84 Ellipsoid müssen
		   gegeben sein. Ausgegeben werden geographische Länge lp
		   und Breite bp (in grad) auf dem Bessel-Ellipsoid.
		   Bei der Transformation werden die Ellipsoidachsen parallel
		   verschoben um dx = -606 m, dy = -23 m und dz = -413 m.*/

		// Geographische Länge lw und Breite bw im WGS84 Datum
		if ($lw == "" || $bw == "") {
			return array(0, 0);
		}
		$lw = doubleval($lw);
		$bw = doubleval($bw);

		// Quellsystem WGS 84 Datum
		// Große Halbachse a und Abplattung fq
		$a = 6378137.000;
		$fq = 3.35281066e-3;

		// Zielsystem Potsdam Datum
		// Abplattung f
		$f = $fq - 1.003748e-5;

		// Parameter für datum shift
		$dx = -606;
		$dy = -23;
		$dz = -413;

		// Quadrat der ersten numerischen Exzentrizität in Quell- und Zielsystem
		$e2q = (2 * $fq - $fq * $fq);
		$e2 = (2 * $f - $f * $f);

		// Breite und Länge in Radianten
		$b1 = $bw * (pi() / 180);
		$l1 = $lw * (pi() / 180);

		// Querkrümmungshalbmesser nd
		$nd = $a / sqrt(1 - $e2q * sin($b1) * sin($b1));

		// Kartesische Koordinaten des Quellsystems WGS84
		$xw = $nd * cos($b1) * cos($l1);
		$yw = $nd * cos($b1) * sin($l1);
		$zw = (1 - $e2q) * $nd * sin($b1);

		// Kartesische Koordinaten des Zielsystems (datum shift) Potsdam
		$x = $xw + $dx;
		$y = $yw + $dy;
		$z = $zw + $dz;

		// Berechnung von Breite und Länge im Zielsystem
		$rb = sqrt($x * $x + $y * $y);
		$b2 = (180 / pi()) * atan(($z / $rb) / (1 - $e2));

		if ($x > 0)
		{
			$l2 = (180 / pi()) * atan($y / $x);
		}
		else if ($x < 0 && $y > 0)
		{
			$l2 = (180 / pi()) * atan($y / $x) + 180;
		}
		else
		{
			$l2 = (180 / pi()) * atan($y / $x) - 180;
		}

		return array($l2, $b2);
	}

	function geo2gk($bp, $lp)
	{
		/* Copyright (c) 2006, HELMUT H. HEIMEIER
		   Permission is hereby granted, free of charge, to any person obtaining a
		   copy of this software and associated documentation files (the "Software"),
		   to deal in the Software without restriction, including without limitation
		   the rights to use, copy, modify, merge, publish, distribute, sublicense,
		   and/or sell copies of the Software, and to permit persons to whom the
		   Software is furnished to do so, subject to the following conditions:
		   The above copyright notice and this permission notice shall be included
		   in all copies or substantial portions of the Software.*/

		/* Die Funktion wandelt geographische Koordinaten in GK Koordinaten
		   um. Geographische Länge lp und Breite bp müssen im Potsdam Datum
		   gegeben sein. Berechnet werden Rechtswert rw und Hochwert hw.*/

		//Geographische Länge lp und Breite bp im Potsdam Datum
		if ($lp == "" || $bp == "") {
			return array(0, 0);
		}
		$lp = doubleval($lp);
		$bp = doubleval($bp);

		// Potsdam Datum
		// Große Halbachse a und Abplattung f
		$a = 6377397.155; // + $falseeasting;
		$f = 3.34277321e-3;

		// Polkrümmungshalbmesser c
		$c = $a / (1 - $f);

		// Quadrat der zweiten numerischen Exzentrizität
		$ex2 = (2 * $f - $f * $f) / ((1 - $f) * (1 - $f));
		$ex4 = $ex2 * $ex2;
		$ex6 = $ex4 * $ex2;
		$ex8 = $ex4 * $ex4;

		// Koeffizienten zur Berechnung der Meridianbogenlänge
		$e0 = $c * (pi() / 180) * (1 - 3 * $ex2 / 4 + 45 * $ex4 / 64 - 175 * $ex6 / 256 + 11025 * $ex8 / 16384);
		$e2 = $c * (-3 * $ex2 / 8 + 15 * $ex4 / 32 - 525 * $ex6 / 1024 + 2205 * $ex8 / 4096);
		$e4 = $c * (15 * $ex4 / 256 - 105 * $ex6 / 1024 + 2205 * $ex8 / 16384);
		$e6 = $c * (-35 * $ex6 / 3072 + 315 * $ex8 / 12288);

		// Breite in Radianten
		$br = $bp * pi() / 180;

		$tan1 = tan($br);
		$tan2 = $tan1 * $tan1;
		$tan4 = $tan2 * $tan2;

		$cos1 = cos($br);
		$cos2 = $cos1 * $cos1;
		$cos4 = $cos2 * $cos2;
		$cos3 = $cos2 * $cos1;
		$cos5 = $cos4 * $cos1;

		$etasq = $ex2 * $cos2;

		// Querkrümmungshalbmesser nd
		$nd = $c / sqrt(1 + $etasq);

		// Meridianbogenlänge g aus gegebener geographischer Breite bp
		$g = $e0 * $bp + $e2 * sin(2 * $br) + $e4 * sin(4 * $br) + $e6 * sin(6 * $br);

		// Längendifferenz dl zum Bezugsmeridian lh
		$kz = round($lp / 3);
		$lh = $kz * 3;
		$dl = ($lp - $lh) * pi() / 180;
		$dl2 = $dl * $dl;
		$dl4 = $dl2 * $dl2;
		$dl3 = $dl2 * $dl;
		$dl5 = $dl4 * $dl;

		// Hochwert hw und Rechtswert rw als Funktion von geographischer Breite und Länge
		$hw = ($g + $nd * $cos2 * $tan1 * $dl2 / 2 + $nd * $cos4 * $tan1 * (5 - $tan2 + 9 * $etasq)
			* $dl4 / 24);
		$rw = ($nd * $cos1 * $dl + $nd * $cos3 * (1 - $tan2 + $etasq) * $dl3 / 6 +
			$nd * $cos5 * (5 - 18 * $tan2 + $tan4) * $dl5 / 120 + $kz * 1e6 + 500000);

		$nk = $hw - intval($hw);
		if ($nk < 0.5) $hw = intval($hw);
		else $hw = intval($hw) + 1;

		$nk = $rw - intval($rw);
		if ($nk < 0.5) $rw = intval($rw);
		else $rw = intval($rw + 1);

		return array($rw, $hw);
	}

	// return string
	function getRD()
	{
		// X0,Y0             Base RD coordinates Amersfoort
		$rdx_base = 155000;
		$rdy_base = 463000;
		// ?0, ?0            Same base, but as wgs84 coordinates
		$lat_base = 52.15517440;
		$lon_base = 5.38720621;

		for ($i = 0; $i <= 6; $i++)
		{
			for ($j = 0; $j <= 5; $j++)
			{
				$rpq[$i][$j] = 0;
				$spq[$i][$j] = 0;
			}
		}
		//#coefficients
		$rpq[0][1] = 190094.945;
		$rpq[1][1] = -11832.228;
		$rpq[2][1] = -114.221;
		$rpq[0][3] = -32.391;
		$rpq[1][0] = -0.705;
		$rpq[3][1] = -2.340;
		$rpq[1][3] = -0.608;
		$rpq[0][2] = -0.008;
		$rpq[2][3] = 0.148;

		$spq[1][0] = 309056.544;
		$spq[0][2] = 3638.893;
		$spq[2][0] = 73.077;
		$spq[1][2] = -157.984;
		$spq[3][0] = 59.788;
		$spq[0][1] = 0.433;
		$spq[2][2] = -6.439;
		$spq[1][1] = -0.032;
		$spq[0][4] = 0.092;
		$spq[1][4] = -0.054;

		// Calculate X, Y of origin
		$latDiff = $this->nLat - $lat_base;
		$dlat = 0.36 * $latDiff;
		$lonDiff = $this->nLon - $lon_base;
		$dlon = 0.36 * $lonDiff;
		$xOrigin = 0;
		$yOrigin = 0;

		for ($q = 0; $q <= 5; $q++)
		{
			for ($p = 0; $p <= 6; $p++)
			{
				$xOrigin = $xOrigin + ($rpq[$p][$q] * ((pow($dlat, $p)) * (pow($dlon, $q))));
				$yOrigin = $yOrigin + ($spq[$p][$q] * ((pow($dlat, $p)) * (pow($dlon, $q))));
			}
		}
		$xOrigin = $xOrigin + $rdx_base;
		$yOrigin = $yOrigin + $rdy_base;

		return 'X ' . floor($xOrigin) . ' Y ' . floor($yOrigin);
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

	function getW3W($language = "de")
	{
        global $opt;

		if (!$opt['lib']['w3w']['apikey'])
			return false;

		$params = array(
			'key' => $opt['lib']['w3w']['apikey'],
			'position' => sprintf('%f,%f', $this->nLat, $this->nLon),
			'lang' => $language,
		);
		$params_str = http_build_query($params);
		$context = stream_context_create( array(
			'http' => array(
				'method' => 'POST',
				'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
							"Content-Length: " . strlen($params_str) . "\r\n",
				'content' => $params_str,
			),
		));
		
		$result = @file_get_contents('http://api.what3words.com/position', false, $context);
		if ($result === false) {
			return false;
		}
		$json = json_decode($result, true);
		if (!isset($json['words']))
			return false;
		return implode('.', $json['words']);
	}
}
?>
