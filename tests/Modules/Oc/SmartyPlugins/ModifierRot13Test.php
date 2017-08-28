<?php

namespace OcTest\Modules\Oc\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.rot13html.php';

class ModifierRot13Test extends AbstractModuleTest
{
    public function testRot13()
    {
        self::assertEquals(
            'Yberz vcfhz qbybe fvg nzrg,',
            \smarty_modifier_rot13html('Lorem ipsum dolor sit amet,')
        );

        self::assertEquals(
            ',,,',
            \smarty_modifier_rot13html(',,,')
        );

        self::assertEquals(
            '',
            \smarty_modifier_rot13html('')
        );
    }
}
