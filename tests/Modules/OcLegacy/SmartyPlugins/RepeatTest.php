<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.repeat.php';

class RepeatTest extends AbstractModuleTest
{
    public function testRepeat(): void
    {
        $smarty = null;
        self::assertEquals(
            'stringstring',
            \smarty_function_repeat(['string' => 'string', 'count' => 2], $smarty)
        );
    }
}
