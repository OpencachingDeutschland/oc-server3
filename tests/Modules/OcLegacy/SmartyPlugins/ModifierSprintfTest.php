<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.sprintf.php';

class ModifierSprintfTest extends AbstractModuleTest
{
    public function testSprintf(): void
    {
        self::assertEquals(
            '000000123',
            \smarty_modifier_sprintf(123, "%'.09d")
        );
    }
}
