<?php

namespace OcTest\Modules\Oc\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.escapejs.php';

class ModifierEscapeJsTest extends AbstractModuleTest
{
    public function testEscapeJs()
    {
        self::assertEquals(
            '&quot;',
            \smarty_modifier_escapejs('"')
        );

        self::assertEquals(
            '\\\\',
            \smarty_modifier_escapejs('\\')
        );

        self::assertEquals(
            '\\\'',
            \smarty_modifier_escapejs('\'')
        );
    }
}
