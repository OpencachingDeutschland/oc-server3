<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.instr.php';

/**
 * Class RandTest
 */
class InstrTest extends AbstractModuleTest
{
    /**
     * Tests if $needle is found in $haystack.
     */
    public function testInString(): void
    {
        self::assertEquals(true, $this->executeSmartyFunction('abcdefghij', 'defg'));
    }

    /**
     * Tests if $needle is found in $haystack.
     */
    public function testNotInString(): void
    {
        self::assertEquals(false, $this->executeSmartyFunction('abcdefghij', 'foo'));
    }

    /**
     * Helper function to execute smarty function.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    private function executeSmartyFunction($haystack, $needle)
    {
        $smarty = null;

        $params = [
            'haystack' => $haystack,
            'needle' => $needle,
        ];

        return \smarty_function_instr($params, $smarty);
    }
}
