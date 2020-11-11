<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.urlencode.php';

class ModifierUrlEncodeTest extends AbstractModuleTest
{
    public function testUrlEncode(): void
    {
        self::assertEquals(
            'öäüäöä123$%&&$',
            urldecode(\smarty_modifier_urlencode('öäüäöä123$%&&$'))
        );
    }
}
