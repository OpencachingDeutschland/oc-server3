<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.base64encode.php';

class ModifierBase64EncodeTest extends AbstractModuleTest
{
    public function testUrlEncode(): void
    {
        self::assertEquals(
            'öäüäöä123$%&&$!"§!$!§%!&&/!I)?=()?',
            base64_decode(\smarty_modifier_base64encode('öäüäöä123$%&&$!"§!$!§%!&&/!I)?=()?'))
        );
    }
}
