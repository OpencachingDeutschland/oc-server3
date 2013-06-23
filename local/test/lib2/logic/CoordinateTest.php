<?php
/****************************************************************************
For license information see doc/license.txt

Unicode Reminder メモ

Coordinate Calculation Test
 ****************************************************************************/
/*
Testdaten:
OCADD6 Nordhalbkugel, Westen (USA)
N 51.52775° W 120.89720°

OC9400 Südhalbkugel, Osten (Afrika)
S 08.81687° E 013.24057°

OC6437 Europa (Niederlande)
N 52.67578° E 006.77300°

OC85A9 Europa (Norwegen)
N 60.63367° E 004.81313°
*/

require '../../../../htdocs/lib2/logic/coordinate.class.php';

class CoordinateTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
	}

	public function tearDown()
	{
	}

	public function testUTM()
	{
		$coord = new coordinate(51.52775, -120.89720);
		$utm = $coord->getUTM();

		$this->assertEquals('10', $utm['zone']);
		$this->assertEquals('U', $utm['letter']);
		$this->assertEquals('N 5710611', $utm['north']);
		$this->assertEquals('E 645865', $utm['east']);

		$coord = new coordinate(-8.81687, 13.24057);
		$utm = $coord->getUTM();

		$this->assertEquals('33', $utm['zone']);
		$this->assertEquals('L', $utm['letter']);
		$this->assertEquals('N 9024939', $utm['north']);
		$this->assertEquals('E 306489', $utm['east']);

		$coord = new coordinate(52.67578, 6.77300);
		$utm = $coord->getUTM();

		$this->assertEquals('32', $utm['zone']);
		$this->assertEquals('U', $utm['letter']);
		$this->assertEquals('N 5838532', $utm['north']);
		$this->assertEquals('E 349438', $utm['east']);

		$coord = new coordinate(60.63367, 4.81313);
		$utm = $coord->getUTM();

		$this->assertEquals('32', $utm['zone']);
		$this->assertEquals('V', $utm['letter']);
		$this->assertEquals('N 6729280', $utm['north']);
		$this->assertEquals('E 271052', $utm['east']);

		$coord = new coordinate(58.682079, 5.793595);
		$utm = $coord->getUTM();

		$this->assertEquals('32', $utm['zone']);
		$this->assertEquals('V', $utm['letter']);
		$this->assertEquals('N 6509097', $utm['north']);
		$this->assertEquals('E 314134', $utm['east']);
	}

	public function testGK()
	{
		$coord = new coordinate(51.52775, -120.89720);
		$gk = $coord->getGK();

		$this->assertEquals('R -39562771 H 5710022', $gk);

		$coord = new coordinate(-8.81687, 13.24057);
		$gk = $coord->getGK();

		$this->assertEquals('R 4636588 H -975608', $gk);

		$coord = new coordinate(52.67578, 6.77300);
		$gk = $coord->getGK();

		$this->assertEquals('R 2552325 H 5838386', $gk);

		$coord = new coordinate(60.63367, 4.81313);
		$gk = $coord->getGK();

		$this->assertEquals('R 2435086 H 6724824', $gk);
	}

	public function testSwissGrid()
	{
		$coord = new coordinate(51.52775, -120.89720);
		$swissGrid = $coord->getSwissGrid();

		$this->assertEquals('-3944504 / 8019927', $swissGrid['coord']);

		$coord = new coordinate(-8.81687, 13.24057);
		$swissGrid = $coord->getSwissGrid();

		$this->assertEquals('1499586 / -6904936', $swissGrid['coord']);

		$coord = new coordinate(52.67578, 6.77300);
		$swissGrid = $coord->getSwissGrid();

		$this->assertEquals('554738 / 837985', $swissGrid['coord']);

		$coord = new coordinate(60.63367, 4.81313);
		$swissGrid = $coord->getSwissGrid();

		$this->assertEquals('451121 / 1739767', $swissGrid['coord']);
	}

	public function testDutchGrid()
	{
		// DutchGrid Berechnungen funktionieren nur auf dem Nord-Ost-Quadranten
		$coord = new coordinate(52.67578, 6.77300);
		$dutchGrid = $coord->getRD();

		$this->assertEquals('X 248723 Y 521824', $dutchGrid);

		$coord = new coordinate(60.63367, 4.81313);
		$dutchGrid = $coord->getRD();

		$this->assertEquals('X 123409 Y 1408833', $dutchGrid);
	}

	public function testQTHLocator()
	{
		$coord = new coordinate(51.52775, -120.89720);
		$qthLocator = $coord->getQTH();

		$this->assertEquals('CO91NM', $qthLocator);

		$coord = new coordinate(-8.81687, 13.24057);
		$qthLocator = $coord->getQTH();

		$this->assertEquals('JI61OE', $qthLocator);

		$coord = new coordinate(52.67578, 6.77300);
		$qthLocator = $coord->getQTH();

		$this->assertEquals('JO32JQ', $qthLocator);

		$coord = new coordinate(60.63367, 4.81313);
		$qthLocator = $coord->getQTH();

		$this->assertEquals('JP20JP', $qthLocator);
	}

	public function testFormatConversions()
	{
		$coord = new coordinate(51.52775, -120.89720);
		$decimalMin = $coord->getDecimalMinutes();

		$this->assertEquals("N 51° 31.665'", $decimalMin['lat']);
		$this->assertEquals("W 120° 53.832'", $decimalMin['lon']);

		$decimalMinSec = $coord->getDecimalMinutesSeconds();

		$this->assertEquals("N 51° 31' 39''", $decimalMinSec['lat']);
		$this->assertEquals("W 120° 53' 49''", $decimalMinSec['lon']);

		$coord = new coordinate(-8.81687, 13.24057);
		$decimalMin = $coord->getDecimalMinutes();

		$this->assertEquals("S 08° 49.012'", $decimalMin['lat']);
		$this->assertEquals("E 013° 14.434'", $decimalMin['lon']);

		$decimalMinSec = $coord->getDecimalMinutesSeconds();

		$this->assertEquals("S 08° 49' 00''", $decimalMinSec['lat']);
		$this->assertEquals("E 013° 14' 26''", $decimalMinSec['lon']);

		$coord = new coordinate(52.67578, 6.77300);
		$decimalMin = $coord->getDecimalMinutes();

		$this->assertEquals("N 52° 40.547'", $decimalMin['lat']);
		$this->assertEquals("E 006° 46.380'", $decimalMin['lon']);

		$decimalMinSec = $coord->getDecimalMinutesSeconds();

		$this->assertEquals("N 52° 40' 32''", $decimalMinSec['lat']);
		$this->assertEquals("E 006° 46' 22''", $decimalMinSec['lon']);

		$coord = new coordinate(60.63367, 4.81313);
		$decimalMin = $coord->getDecimalMinutes();

		$this->assertEquals("N 60° 38.020'", $decimalMin['lat']);
		$this->assertEquals("E 004° 48.788'", $decimalMin['lon']);

		$decimalMinSec = $coord->getDecimalMinutesSeconds();

		$this->assertEquals("N 60° 38' 01''", $decimalMinSec['lat']);
		$this->assertEquals("E 004° 48' 47''", $decimalMinSec['lon']);
	}
}
