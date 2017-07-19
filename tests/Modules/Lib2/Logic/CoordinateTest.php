<?php
/****************************************************************************
 * For license information see LICENSE.md
 *
 *
 * Coordinate Calculation Test
 ****************************************************************************/

namespace OcTest\Modules\Lib2\Logic;

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

class CoordinateTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        global $opt;
        $opt['lib']['w3w']['apikey'] = 'X27PDW41';
    }


    public function utmProvider()
    {
        return [
            [51.52775, -120.89720, '10', 'U', 'N 5710611', 'E 645865'],
            [-8.81687, 13.24057, '33', 'L', 'N 9024939', 'E 306489'],
            [52.67578, 6.77300, '32', 'U', 'N 5838532', 'E 349438'],
            [60.63367, 4.81313, '32', 'V', 'N 6729280', 'E 271052'],
            [58.682079, 5.793595, '32', 'V', 'N 6509097', 'E 314134'],
        ];
    }

    /**
     * @group unit-tests
     * @covers       \coordinate::getUTM()
     * @dataProvider utmProvider
     *
     * @return void
     */
    public function testUTM($lat, $lon, $zone, $letter, $north, $east)
    {
        $coord = new \coordinate($lat, $lon);
        $utm = $coord->getUTM();

        self::assertEquals($zone, $utm['zone']);
        self::assertEquals($letter, $utm['letter']);
        self::assertEquals($north, $utm['north']);
        self::assertEquals($east, $utm['east']);
    }

    public function gkProvider()
    {
        return [
            [51.52775, -120.89720, 'R -39562771 H 5710022'],
            [-8.81687, 13.24057, 'R 4636588 H -975608'],
            [52.67578, 6.77300, 'R 2552325 H 5838386'],
            [60.63367, 4.81313, 'R 2435086 H 6724824'],
        ];
    }

    /**
     * @group unit-tests
     * @covers       \coordinate::getGK()
     * @dataProvider gkProvider
     *
     * @return void
     */

    public function testGK($lat, $lon, $expectedGK)
    {
        $coord = new \coordinate($lat, $lon);
        $gk = $coord->getGK();

        self::assertEquals($expectedGK, $gk);
    }

    public function swissGridProvider()
    {
        return [
            [51.52775, -120.89720, '-3944504 / 8019927'],
            [-8.81687, 13.24057, '1499586 / -6904936'],
            [52.67578, 6.77300, '554738 / 837985'],
            [60.63367, 4.81313, '451121 / 1739767'],
        ];
    }

    /**
     * @group unit-tests
     * @covers       \coordinate::getSwissGrid()
     * @dataProvider swissGridProvider
     *
     * @return void
     */
    public function testSwissGrid($lat, $lon, $expectedSG)
    {
        $coord = new \coordinate($lat, $lon);
        $swissGrid = $coord->getSwissGrid();

        self::assertEquals($expectedSG, $swissGrid['coord']);
    }

    public function dutchGridProvider()
    {
        return [
            [52.67578, 6.77300, 'X 248723 Y 521824'],
            [60.63367, 4.81313, 'X 123409 Y 1408833'],
        ];
    }

    /**
     * @group unit-tests
     * @covers       \coordinate::getRD()
     * @dataProvider dutchGridProvider
     *
     * @return void
     */
    public function testDutchGrid($lat, $lon, $expectedDG)
    {
        // DutchGrid Berechnungen funktionieren nur auf dem Nord-Ost-Quadranten
        $coord = new \coordinate($lat, $lon);
        $dutchGrid = $coord->getRD();

        self::assertEquals($expectedDG, $dutchGrid);
    }

    public function qthProvider()
    {
        return [
            [51.52775, -120.89720, 'CO91NM'],
            [-8.81687, 13.24057, 'JI61OE'],
            [52.67578, 6.77300, 'JO32JQ'],
            [60.63367, 4.81313, 'JP20JP'],
        ];
    }

    /**
     * @group unit-tests
     * @covers       \coordinate::getQTH()
     * @dataProvider qthProvider
     *
     * @return void
     */
    public function testQTHLocator($lat, $lon, $expectedQTH)
    {
        $coord = new \coordinate($lat, $lon);
        $qthLocator = $coord->getQTH();

        self::assertEquals($expectedQTH, $qthLocator);
    }

    public function what3WordsProvider()
    {
        return [
            [52.473570, 13.371317, 'DE', 'gewinn.kopf.digitalen'],
            [60.168947, 24.958826, 'DE', 'kurzem.knie.ringen'],
            [45.999639, -1.213892, 'DE', 'bewohnbar.modernes.empfundenen'],
            [52.473570, 13.371317, 'EN', 'steer.removed.smashes'],
            [60.168947, 24.958826, 'EN', 'dished.mailing.starred'],
            [45.999639, -1.213892, 'EN', 'declaim.alright.loaning'],
        ];
    }

    /**
     * @group unit-tests
     * @covers       \coordinate::getW3W
     * @dataProvider what3WordsProvider
     *
     * @param $lat
     * @param $lon
     * @param $language
     * @param $expectedW3W
     */
    public function testWhat3Words($lat, $lon, $language, $expectedW3W)
    {
        $coord = new \coordinate($lat, $lon);
        $w3w = $coord->getW3W($language);

        self::assertEquals($expectedW3W, $w3w);
    }

    public function formatProvider()
    {
        return [
            [
                51.52775,
                -120.89720,
                ['lat' => "N 51° 31.665'", 'lon' => "W 120° 53.832'"],
                ['lat' => "N 51° 31' 39''", 'lon' => "W 120° 53' 49''"]
            ],
            [
                -8.81687,
                13.24057,
                ['lat' => "S 08° 49.012'", 'lon' => "E 013° 14.434'"],
                ['lat' => "S 08° 49' 00''", 'lon' => "E 013° 14' 26''"]
            ],
            [
                52.67578,
                6.77300,
                ['lat' => "N 52° 40.547'", 'lon' => "E 006° 46.380'"],
                ['lat' => "N 52° 40' 32''", 'lon' => "E 006° 46' 22''"]
            ],
            [
                60.63367,
                4.81313,
                ['lat' => "N 60° 38.020'", 'lon' => "E 004° 48.788'"],
                ['lat' => "N 60° 38' 01''", 'lon' => "E 004° 48' 47''"]
            ],
        ];
    }

    /**
     * @group unit-tests
     * @covers       \coordinate::getDecimalMinutes
     * @dataProvider formatProvider
     *
     * @return void
     */
    public function testFormatConversions($lat, $lon, $expectedMin, $expectedMinSec)
    {
        $coord = new \coordinate($lat, $lon);

        self::assertEquals($expectedMin, $coord->getDecimalMinutes());
        self::assertEquals($expectedMinSec, $coord->getDecimalMinutesSeconds());
    }
}
