<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.format_hour.php';

class ModifierFormatHourTest extends AbstractModuleTest
{
    public function testFormatHour(): void
    {
        self::assertEquals(
            '1:30',
            \smarty_modifier_format_hour('1.5')
        );
    }
}
