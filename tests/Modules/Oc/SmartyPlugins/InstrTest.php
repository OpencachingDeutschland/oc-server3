<?php

namespace OcTest\Modules\Oc\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/Oc/SmartyPlugins/function.instr.php';

/**
 * Class RandTest
 *
 * @package OcTest\Modules\Oc\SmartyPlugins
 */
class InstrTest extends AbstractModuleTest
{
    /**
     * Tests if $needle is found in $haystack.
     *
     * @return void
     */
    public function testInString()
    {
        self::assertEquals(true, $this->executeSmartyFunction('abcdefghij', 'defg'));
    }

    /**
     * Tests if $needle is found in $haystack.
     *
     * @return void
     */
    public function testNotInString()
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
            'needle' => $needle
        ];

        return \smarty_function_instr($params, $smarty);
    }
}
