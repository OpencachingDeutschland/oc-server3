<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Coordinate Calculation Test
 ****************************************************************************/

namespace Oc\Modules\Lib2\Logic;

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
        $opt['lib']['w3w']['apikey'] = 'GETAPIKEY';
    }


    public function utmProvider()
    {
        return array(
            array(51.52775, -120.89720, '10', 'U', 'N 5710611', 'E 645865'),
            array(-8.81687, 13.24057, '33', 'L', 'N 9024939', 'E 306489'),
            array(52.67578, 6.77300, '32', 'U', 'N 5838532', 'E 349438'),
            array(60.63367, 4.81313, '32', 'V', 'N 6729280', 'E 271052'),
            array(58.682079, 5.793595, '32', 'V', 'N 6509097', 'E 314134'),
        );
    }

    /**
     * @group unit-tests
     * @covers \coordinate::getUTM()
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
        return array(
            array(51.52775, -120.89720, 'R -39562771 H 5710022'),
            array(-8.81687, 13.24057, 'R 4636588 H -975608'),
            array(52.67578, 6.77300, 'R 2552325 H 5838386'),
            array(60.63367, 4.81313, 'R 2435086 H 6724824'),
        );
    }

    /**
     * @group unit-tests
     * @covers \coordinate::getGK()
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
        return array(
            array(51.52775, -120.89720, '-3944504 / 8019927'),
            array(-8.81687, 13.24057, '1499586 / -6904936'),
            array(52.67578, 6.77300, '554738 / 837985'),
            array(60.63367, 4.81313, '451121 / 1739767'),
        );
    }

    /**
     * @group unit-tests
     * @covers \coordinate::getSwissGrid()
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
        return array(
            array(52.67578, 6.77300, 'X 248723 Y 521824'),
            array(60.63367, 4.81313, 'X 123409 Y 1408833'),
        );
    }

    /**
     * @group unit-tests
     * @covers \coordinate::getRD()
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
        return array(
            array(51.52775, -120.89720, 'CO91NM'),
            array(-8.81687, 13.24057, 'JI61OE'),
            array(52.67578, 6.77300, 'JO32JQ'),
            array(60.63367, 4.81313, 'JP20JP'),
        );
    }

    /**
     * @group unit-tests
     * @covers \coordinate::getQTH()
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
        return array(
            array(52.473570, 13.371317, 'DE', 'gewinn.kopf.digitalen'),
            array(60.168947, 24.958826, 'DE', 'kurzem.knie.ringen'),
            array(45.999639, -1.213892, 'DE', 'bewohnbar.modernes.empfundenen'),
            array(52.473570, 13.371317, 'EN', 'steer.removed.smashes'),
            array(60.168947, 24.958826, 'EN', 'dished.mailing.starred'),
            array(45.999639, -1.213892, 'EN', 'declaim.alright.loaning'),
        );
    }

    /**
     * @group unit-tests
     * @covers \coordinate::getW3W
     * @dataProvider what3WordsProvider
     *
     * @return void
     */
    public function need_api_key_testWhat3Words($lat, $lon, $language, $expectedW3W)
    {
        $coord = new \coordinate($lat, $lon);
        $w3w   = $coord->getW3W($language);

        $this->assertEquals($expectedW3W, $w3w);
    }

    public function formatProvider()
    {
        return array(
            array(51.52775, -120.89720, array('lat'=>"N 51° 31.665'", 'lon'=>"W 120° 53.832'"), array('lat'=>"N 51° 31' 39''", 'lon'=>"W 120° 53' 49''")),
            array(-8.81687, 13.24057, array('lat'=>"S 08° 49.012'", 'lon'=>"E 013° 14.434'"), array('lat'=>"S 08° 49' 00''", 'lon'=>"E 013° 14' 26''")),
            array(52.67578, 6.77300, array('lat'=>"N 52° 40.547'", 'lon'=>"E 006° 46.380'"), array('lat'=>"N 52° 40' 32''", 'lon'=>"E 006° 46' 22''")),
            array(60.63367, 4.81313, array('lat'=>"N 60° 38.020'", 'lon'=>"E 004° 48.788'"), array('lat'=>"N 60° 38' 01''", 'lon'=>"E 004° 48' 47''")),
        );
    }

    /**
     * @group unit-tests
     * @covers \coordinate::getDecimalMinutes
     * @dataProvider formatProvider
     *
     * @return void
     */
    public function testFormatConversions($lat, $lon, $expectedMin, $expectedMinSec)
    {
        $coord = new \coordinate($lat, $lon);

        $this->assertEquals($expectedMin, $coord->getDecimalMinutes());
        $this->assertEquals($expectedMinSec, $coord->getDecimalMinutesSeconds());
    }
}
