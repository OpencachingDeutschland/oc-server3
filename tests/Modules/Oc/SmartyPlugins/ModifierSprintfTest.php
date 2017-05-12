<?php

namespace OcTest\Modules\Oc\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/Oc/SmartyPlugins/modifier.sprintf.php';

class ModifierSprintfTest extends AbstractModuleTest
{
    public function testSprintf()
    {
        self::assertEquals(
            '000000123',
            \smarty_modifier_sprintf(123, "%'.09d")
        );
    }
}
