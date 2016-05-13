<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Coordinate Calculation Test
 ****************************************************************************/

namespace Oc\Modules\Lib2\Logic;

use Oc\Modules\AbstractModuleTest;

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
class CoordinateTest extends AbstractModuleTest
{
    /**
     * @covers \coordinate::getUTM()
     */
    public function testUTM()
    {
        $coord = new \coordinate(51.52775, - 120.89720);
        $utm = $coord->getUTM();

        self::assertEquals('10', $utm['zone']);
        self::assertEquals('U', $utm['letter']);
        self::assertEquals('N 5710611', $utm['north']);
        self::assertEquals('E 645865', $utm['east']);

        $coord = new \coordinate(- 8.81687, 13.24057);
        $utm = $coord->getUTM();

        self::assertEquals('33', $utm['zone']);
        self::assertEquals('L', $utm['letter']);
        self::assertEquals('N 9024939', $utm['north']);
        self::assertEquals('E 306489', $utm['east']);

        $coord = new \coordinate(52.67578, 6.77300);
        $utm = $coord->getUTM();

        self::assertEquals('32', $utm['zone']);
        self::assertEquals('U', $utm['letter']);
        self::assertEquals('N 5838532', $utm['north']);
        self::assertEquals('E 349438', $utm['east']);

        $coord = new \coordinate(60.63367, 4.81313);
        $utm = $coord->getUTM();

        self::assertEquals('32', $utm['zone']);
        self::assertEquals('V', $utm['letter']);
        self::assertEquals('N 6729280', $utm['north']);
        self::assertEquals('E 271052', $utm['east']);

        $coord = new \coordinate(58.682079, 5.793595);
        $utm = $coord->getUTM();

        self::assertEquals('32', $utm['zone']);
        self::assertEquals('V', $utm['letter']);
        self::assertEquals('N 6509097', $utm['north']);
        self::assertEquals('E 314134', $utm['east']);
    }

    /**
     * @covers \coordinate::getGK()
     */

    public function testGK()
    {
        $coord = new \coordinate(51.52775, - 120.89720);
        $gk = $coord->getGK();

        self::assertEquals('R -39562771 H 5710022', $gk);

        $coord = new \coordinate(- 8.81687, 13.24057);
        $gk = $coord->getGK();

        self::assertEquals('R 4636588 H -975608', $gk);

        $coord = new \coordinate(52.67578, 6.77300);
        $gk = $coord->getGK();

        self::assertEquals('R 2552325 H 5838386', $gk);

        $coord = new \coordinate(60.63367, 4.81313);
        $gk = $coord->getGK();

        self::assertEquals('R 2435086 H 6724824', $gk);
    }

    /**
     * @covers \coordinate::getSwissGrid()
     */
    public function testSwissGrid()
    {
        $coord = new \coordinate(51.52775, - 120.89720);
        $swissGrid = $coord->getSwissGrid();

        self::assertEquals('-3944504 / 8019927', $swissGrid['coord']);

        $coord = new \coordinate(- 8.81687, 13.24057);
        $swissGrid = $coord->getSwissGrid();

        self::assertEquals('1499586 / -6904936', $swissGrid['coord']);

        $coord = new \coordinate(52.67578, 6.77300);
        $swissGrid = $coord->getSwissGrid();

        self::assertEquals('554738 / 837985', $swissGrid['coord']);

        $coord = new \coordinate(60.63367, 4.81313);
        $swissGrid = $coord->getSwissGrid();

        self::assertEquals('451121 / 1739767', $swissGrid['coord']);
    }

    /**
     * @covers \coordinate::getRD()
     */
    public function testDutchGrid()
    {
        // DutchGrid Berechnungen funktionieren nur auf dem Nord-Ost-Quadranten
        $coord = new \coordinate(52.67578, 6.77300);
        $dutchGrid = $coord->getRD();

        self::assertEquals('X 248723 Y 521824', $dutchGrid);

        $coord = new \coordinate(60.63367, 4.81313);
        $dutchGrid = $coord->getRD();

        self::assertEquals('X 123409 Y 1408833', $dutchGrid);
    }

    /**
     * @covers \coordinate::getQTH()
     */
    public function testQTHLocator()
    {
        $coord = new \coordinate(51.52775, - 120.89720);
        $qthLocator = $coord->getQTH();

        self::assertEquals('CO91NM', $qthLocator);

        $coord = new \coordinate(- 8.81687, 13.24057);
        $qthLocator = $coord->getQTH();

        self::assertEquals('JI61OE', $qthLocator);

        $coord = new \coordinate(52.67578, 6.77300);
        $qthLocator = $coord->getQTH();

        self::assertEquals('JO32JQ', $qthLocator);

        $coord = new \coordinate(60.63367, 4.81313);
        $qthLocator = $coord->getQTH();

        self::assertEquals('JP20JP', $qthLocator);
    }

    /**
     * @covers \coordinate::getDecimalMinutes
     */
    public function testFormatConversions()
    {
        $coord = new \coordinate(51.52775, - 120.89720);
        $decimalMin = $coord->getDecimalMinutes();

        self::assertEquals("N 51° 31.665'", $decimalMin['lat']);
        self::assertEquals("W 120° 53.832'", $decimalMin['lon']);

        $decimalMinSec = $coord->getDecimalMinutesSeconds();

        self::assertEquals("N 51° 31' 39''", $decimalMinSec['lat']);
        self::assertEquals("W 120° 53' 49''", $decimalMinSec['lon']);

        $coord = new \coordinate(- 8.81687, 13.24057);
        $decimalMin = $coord->getDecimalMinutes();

        self::assertEquals("S 08° 49.012'", $decimalMin['lat']);
        self::assertEquals("E 013° 14.434'", $decimalMin['lon']);

        $decimalMinSec = $coord->getDecimalMinutesSeconds();

        self::assertEquals("S 08° 49' 00''", $decimalMinSec['lat']);
        self::assertEquals("E 013° 14' 26''", $decimalMinSec['lon']);

        $coord = new \coordinate(52.67578, 6.77300);
        $decimalMin = $coord->getDecimalMinutes();

        self::assertEquals("N 52° 40.547'", $decimalMin['lat']);
        self::assertEquals("E 006° 46.380'", $decimalMin['lon']);

        $decimalMinSec = $coord->getDecimalMinutesSeconds();

        self::assertEquals("N 52° 40' 32''", $decimalMinSec['lat']);
        self::assertEquals("E 006° 46' 22''", $decimalMinSec['lon']);

        $coord = new \coordinate(60.63367, 4.81313);
        $decimalMin = $coord->getDecimalMinutes();

        self::assertEquals("N 60° 38.020'", $decimalMin['lat']);
        self::assertEquals("E 004° 48.788'", $decimalMin['lon']);

        $decimalMinSec = $coord->getDecimalMinutesSeconds();

        self::assertEquals("N 60° 38' 01''", $decimalMinSec['lat']);
        self::assertEquals("E 004° 48' 47''", $decimalMinSec['lon']);
    }
}
