<?php
 /***************************************************************************
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Erstellt stored procedures.
		
	***************************************************************************/

	$opt['rootpath'] = '../../../';
  require_once($opt['rootpath'] . 'lib/clicompatbase.inc.php');

  if (!file_exists($opt['rootpath'] . 'util/mysql_root/sql_root.inc.php'))
		die("\n" . 'install util/mysql_root/sql_root.inc.php' . "\n\n");

  require_once($opt['rootpath'] . 'util/mysql_root/sql_root.inc.php');

/* begin db connect */
	db_root_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */

	sql_dropFunction('distance');
	sql("CREATE FUNCTION `distance` (lat1 DOUBLE, lon1 DOUBLE, lat2 DOUBLE, lon2 DOUBLE) RETURNS DOUBLE DETERMINISTIC 
	     BEGIN
	       RETURN ACOS(COS((90-lat1) * 3.14159 / 180) * COS((90-lat2)* 3.14159 / 180) + SIN((90-lat1) * 3.14159 / 180) * SIN((90-lat2) * 3.14159 / 180) * COS((lon1-lon2) * 3.14159 / 180)) * 6370;
			 END;");

	sql_dropFunction('projLon');
	sql("CREATE FUNCTION `projLon` (nLat DOUBLE, nLon DOUBLE, nDistance DOUBLE, nAngle DOUBLE) RETURNS DOUBLE DETERMINISTIC 
	     BEGIN
			   DECLARE nLatProj DOUBLE DEFAULT 0;
			   DECLARE nDeltaLon DOUBLE DEFAULT 0;
			   DECLARE nLonProj DOUBLE DEFAULT 0;

	       SET nLat = nLat * 3.141592654 / 180;
	       SET nLon = nLon * 3.141592654 / 180;
	       SET nAngle = nAngle * 3.141592654 / 180;
	       SET nDistance = (3.141592654/ (180 * 60)) * nDistance / 1.852;

	       SET nLatProj = asin(sin(nLat) * cos(nDistance) + cos(nLat) * sin(nDistance) * cos(nAngle));
	       SET nDeltaLon = -1 * (atan2(sin(nAngle) * sin(nDistance) * cos(nLat), cos(nDistance) - sin(nLat) * sin(nLatProj)));
	       SET nLonProj = (nLon - nDeltaLon + 3.141592654) - floor((nLon - nDeltaLon + 3.141592654) / 2 / 3.141592654) - 3.141592654;

	       return nLonProj * 180 / 3.141592654;
			 END;");

	sql_dropFunction('projLat');
	sql("CREATE FUNCTION `projLat` (nLat DOUBLE, nLon DOUBLE, nDistance DOUBLE, nAngle DOUBLE) RETURNS DOUBLE DETERMINISTIC 
	     BEGIN
					DECLARE nLatProj DOUBLE DEFAULT 0;

					SET nLat = nLat * 3.141592654 / 180;
					SET nLon = nLon * 3.141592654 / 180;
					SET nAngle = nAngle * 3.141592654 / 180;
					SET nDistance = (3.141592654 / (180 * 60)) * nDistance / 1.852;

					SET nLatProj = asin(sin(nLat) * cos(nDistance) + cos(nLat) * sin(nDistance) * cos(nAngle));

					return nLatProj * 180 / 3.141592654;
			 END;");

	sql_dropFunction('angle');
	sql("CREATE FUNCTION `angle` (nLat1 DOUBLE, nLon1 DOUBLE, nLat2 DOUBLE, nLon2 DOUBLE) RETURNS DOUBLE DETERMINISTIC 
	     BEGIN
					DECLARE nDegCorrection DOUBLE DEFAULT 0;
					DECLARE nEntfernungsWinkel DOUBLE DEFAULT 0;
					DECLARE nArccosEntfernungsWinkel DOUBLE DEFAULT 0;
					DECLARE n DOUBLE DEFAULT 0;
					DECLARE nAngle DOUBLE DEFAULT 0;

					SET nLat1 = nLat1 * 3.141592654 / 180;
					SET nLon1 = nLon1 * 3.141592654 / 180;
					SET nLat2 = nLat2 * 3.141592654 / 180;
					SET nLon2 = nLon2 * 3.141592654 / 180;

					SET nDegCorrection = IF(nLon1 < nLon2, 360, 0);
					SET nEntfernungsWinkel = sin(nLat1) * sin(nLat2) + cos(nLat1) * cos(nLat2) * cos(nLon1 - nLon2);

					IF ((nEntfernungsWinkel < -1.0) OR (nEntfernungsWinkel >= 1.0)) THEN
						RETURN 0;
					END IF;

					SET nArccosEntfernungsWinkel = acos(nEntfernungsWinkel);
					SET n = sin(nLat2) / sin(nArccosEntfernungsWinkel) / cos(nLat1) - tan(nLat1) / tan(nArccosEntfernungsWinkel);

					IF (n < -1.0) OR (n > 1.0) THEN
						IF nLon1 = nLon2 THEN
							IF nLat1 > nLat2 THEN
								RETURN 90.0;
							ELSE
								RETURN 270.0;
							END IF;
						END IF;

						RETURN 0.0;
					ELSE
						SET nAngle = acos(n) * 180.0 / 3.141592654 - nDegCorrection;
						IF nAngle < 0.0 THEN
							RETURN 360 + nAngle;
						ELSE
							RETURN 360 - nAngle;
						END IF;
					END IF;

					RETURN 0;
			 END;");

	sql_dropFunction('ptonline');
	sql("CREATE FUNCTION `ptonline` (nLat DOUBLE, nLon DOUBLE, nLatPt1 DOUBLE, nLonPt1 DOUBLE, nLatPt2 DOUBLE, nLonPt2 DOUBLE, nMaxDistance DOUBLE) RETURNS DOUBLE DETERMINISTIC 
	     BEGIN
					DECLARE nTmpLon DOUBLE DEFAULT 0;
					DECLARE nTmpLat DOUBLE DEFAULT 0;

					DECLARE nAnglePt1Pt2 DOUBLE DEFAULT 0;
					DECLARE nAnglePt1 DOUBLE DEFAULT 0;
					DECLARE nAngleLinePt1 DOUBLE DEFAULT 0;
					DECLARE nAnglePt2 DOUBLE DEFAULT 0;
					DECLARE nAngleLinePt2 DOUBLE DEFAULT 0;
					DECLARE nDistancePt1 DOUBLE DEFAULT 0;
					DECLARE nDistancePt2 DOUBLE DEFAULT 0;

					DECLARE nProjLat DOUBLE DEFAULT 0;
					DECLARE nProjLon DOUBLE DEFAULT 0;
					DECLARE nProjAngle DOUBLE DEFAULT 0;
					DECLARE nAngleProj DOUBLE DEFAULT 0;
					DECLARE nAnglePt1Proj DOUBLE DEFAULT 0;
					
					IF nLonPt2 < nLonPt1 THEN
						SET nTmpLon = nLonPt1; 
						SET nTmpLat = nLatPt1;
						SET nLonPt1 = nLonPt2; 
						SET nLatPt1 = nLatPt2;
						SET nLonPt2 = nTmpLon; 
						SET nLatPt2 = nTmpLat;
					END IF;
					
				  IF nLonPt1 = nLonPt2 THEN 
						SET nLonPt2 = nLonPt2 + 0.000001;
					END IF;

					SET nAnglePt1Pt2 = angle(nLatPt1, nLonPt1, nLatPt2, nLonPt2);
					SET nAnglePt1 = angle(nLatPt1, nLonPt1, nLat, nLon);
					SET nAngleLinePt1 = nAnglePt1Pt2 - nAnglePt1;

					IF nAngleLinePt1 > 180 THEN
						SET nAngleLinePt1 = 360 - nAngleLinePt1;
					END IF;
					IF nAngleLinePt1 < -180 THEN
						SET nAngleLinePt1 = nAngleLinePt1 + 360;
					END IF;

					SET nAnglePt2 = angle(nLat, nLon, nLatPt2, nLonPt2);
					SET nAngleLinePt2 = nAnglePt1Pt2 - nAnglePt2;

					IF nAngleLinePt2 > 180 THEN
						SET nAngleLinePt2 = 360 - nAngleLinePt2;
					END IF;
					IF nAngleLinePt2 < -180 THEN
						SET nAngleLinePt2 = nAngleLinePt2 + 360;
					END IF;

					IF (nAngleLinePt1 > 90) OR (nAngleLinePt1 < -90) THEN
						SET nDistancePt1 = distance(nLat, nLon, nLatPt1, nLonPt1);
						IF nDistancePt1 < nMaxDistance THEN
							RETURN 1;
						ELSE
							RETURN 0;
						END IF;
					END IF;
				  
					IF (nAngleLinePt2 > 90) OR (nAngleLinePt2 < -90) THEN
						SET nDistancePt2 = distance(nLat, nLon, nLatPt2, nLonPt2);
						IF nDistancePt2 < nMaxDistance THEN
							RETURN 1;
						ELSE
							RETURN 0;
						END IF;
					END IF;

					IF nAngleLinePt1 > 0 THEN
						IF nAnglePt1Pt2 > 270 THEN
							SET nProjAngle = nAnglePt1Pt2 - 270;
						ELSE
							SET nProjAngle = nAnglePt1Pt2 + 90;
						END IF;
					ELSE
						IF nAnglePt1Pt2 > 90 THEN
							SET nProjAngle = nAnglePt1Pt2 - 90;
						ELSE
							SET nProjAngle = nAnglePt1Pt2 + 270;
						END IF;
					END IF;

					SET nProjLat = projLat(nLat, nLon, nMaxDistance, nProjAngle);
					SET nProjLon = projLon(nLat, nLon, nMaxDistance, nProjAngle);

					SET nAnglePt1Proj = angle(nLatPt1, nLonPt1, nProjLat, nProjLon);
					SET nAngleProj = nAnglePt1Pt2 - nAnglePt1Proj;
					IF nAngleProj > 180 THEN
						SET nAngleProj = 360 - nAngleProj;
					END IF;
					IF nAngleProj < -180 THEN
						SET nAngleProj = nAngleProj + 360;
					END IF;
				  
					IF (nAngleLinePt1 >= 0) AND (nAngleProj < 0) THEN
						RETURN 1;
					ELSEIF (nAngleLinePt1 < 0) AND (nAngleProj >= 0) THEN
						RETURN 1;
					ELSE
						RETURN 0;
					END IF;
			 END;");
?>
