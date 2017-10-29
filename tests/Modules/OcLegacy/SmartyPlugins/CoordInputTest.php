<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.coordinput.php';

class CoordInputTest extends AbstractModuleTest
{
    public function testRepeat()
    {
        $smarty = null;

        $params = [
            'prefix' => 'coord',
            'lat' => '48.12345',
            'lon' => '9.12345',
        ];

        self::assertEquals(
            '<select name="coordNS"><option value="N" selected="selected">N</option><option value="S">S</option></select>&nbsp;<input type="text" name="coordLat" value="48" size="1" maxlength="2" />&deg; <input type="text" name="coordLatMin" value="07.407" size="5" maxlength="6" /> \'<br /><select name="coordEW"><option value="E" selected="selected">E</option><option value="W">W</option></select>&nbsp;<input type="text" name="coordLon" value="009" size="2" maxlength="3" />&deg; <input type="text" name="coordLonMin" value="07.407" size="5" maxlength="6" /> \'',
            \smarty_function_coordinput($params, $smarty)
        );
    }
}
