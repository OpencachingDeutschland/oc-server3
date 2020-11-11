<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.stripcrlf.php';

class ModifierStripCrLfTest extends AbstractModuleTest
{
    public function testUrlEncode(): void
    {
        self::assertEquals(
            'asdf',
            \smarty_modifier_stripcrlf("\rasdf\n")
        );
    }
}
