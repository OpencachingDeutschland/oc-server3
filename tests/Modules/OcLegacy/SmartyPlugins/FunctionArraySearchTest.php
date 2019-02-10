<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;
use OcTest\Utils\SmartyDummy;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.array_search.php';

class FunctionArraySearchTest extends AbstractModuleTest
{
    public function test_array_search_returns_empty_string(): void
    {
        $smarty = new SmartyDummy();
        self::assertEquals(
            '',
            \smarty_function_array_search(['haystack' => false, 'var' => 'test'], $smarty)
        );

        self::assertEquals(
            '',
            \smarty_function_array_search(
                ['haystack' => ['test123', 'searching'], 'needle' => 'searching', 'var' => 'test'],
                $smarty
            )
        );
    }
}
