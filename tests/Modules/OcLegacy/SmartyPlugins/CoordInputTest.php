<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.coordinput.php';

class CoordInputTest extends AbstractModuleTest
{
    public function test_coordinput(): void
    {
        $params = [
            'prefix' => 'coord',
            'lat' => '48.12345',
            'lon' => '9.12345',
        ];

        self::assertEquals(
            '<select name="coordNS"><option value="N" selected="selected">N</option><option value="S">S</option></select>&nbsp;<input type="text" name="coordLat" value="48" size="1" maxlength="2" />&deg; <input type="text" name="coordLatMin" value="07.407" size="5" maxlength="6" /> \'<br /><select name="coordEW"><option value="E" selected="selected">E</option><option value="W">W</option></select>&nbsp;<input type="text" name="coordLon" value="009" size="2" maxlength="3" />&deg; <input type="text" name="coordLonMin" value="07.407" size="5" maxlength="6" /> \'',
            \smarty_function_coordinput($params)
        );

        $params = [
            'prefix' => 'coord',
            'lat' => -1,
            'lon' => -1,
        ];

        self::assertEquals(
            '<select name="coordNS"><option value="N">N</option><option value="S" selected="selected">S</option></select>&nbsp;<input type="text" name="coordLat" value="01" size="1" maxlength="2" />&deg; <input type="text" name="coordLatMin" value="00.000" size="5" maxlength="6" /> \'<br /><select name="coordEW"><option value="E">E</option><option value="W" selected="selected">W</option></select>&nbsp;<input type="text" name="coordLon" value="001" size="2" maxlength="3" />&deg; <input type="text" name="coordLonMin" value="00.000" size="5" maxlength="6" /> \'',
            \smarty_function_coordinput($params)
        );
    }

    public function test_coordinput_with_laterror(): void
    {
        $params = [
            'prefix' => 'coord',
            'laterror' => true,
            'lat' => 0,
            'lon' => 0,
        ];

        self::assertEquals(
            '<select name="coordNS"><option value="N" selected="selected">N</option><option value="S">S</option></select>&nbsp;<input type="text" name="coordLat" value="00" size="1" maxlength="2" />&deg; <input type="text" name="coordLatMin" value="00.000" size="5" maxlength="6" /> \' &nbsp; <span class="errormsg">Invalid coordinate</span><br /><select name="coordEW"><option value="E" selected="selected">E</option><option value="W">W</option></select>&nbsp;<input type="text" name="coordLon" value="000" size="2" maxlength="3" />&deg; <input type="text" name="coordLonMin" value="00.000" size="5" maxlength="6" /> \'',
            \smarty_function_coordinput($params)
        );
    }

    public function test_coordinput_with_lonerror(): void
    {
        $params = [
            'prefix' => 'coord',
            'lonerror' => true,
            'lat' => 0,
            'lon' => 0,
        ];

        self::assertEquals(
            '<select name="coordNS"><option value="N" selected="selected">N</option><option value="S">S</option></select>&nbsp;<input type="text" name="coordLat" value="00" size="1" maxlength="2" />&deg; <input type="text" name="coordLatMin" value="00.000" size="5" maxlength="6" /> \'<br /><select name="coordEW"><option value="E" selected="selected">E</option><option value="W">W</option></select>&nbsp;<input type="text" name="coordLon" value="000" size="2" maxlength="3" />&deg; <input type="text" name="coordLonMin" value="00.000" size="5" maxlength="6" /> \' &nbsp; <span class="errormsg">Invalid coordinate</span>',
            \smarty_function_coordinput($params)
        );
    }
}
