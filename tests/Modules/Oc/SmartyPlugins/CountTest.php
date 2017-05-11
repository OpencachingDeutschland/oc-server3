<?php

namespace OcTest\Modules\Oc\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/Oc/SmartyPlugins/function.count.php';

class CountTest extends AbstractModuleTest
{
    public function testCount()
    {
        $smarty = null;
        self::assertEquals(
            2,
            \smarty_function_count(['array' => ['1', '2']], $smarty)
        );
    }
}
