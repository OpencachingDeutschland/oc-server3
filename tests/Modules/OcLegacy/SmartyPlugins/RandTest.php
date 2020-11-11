<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.rand.php';

/**
 * Class RandTest
 */
class RandTest extends AbstractModuleTest
{
    public function testEmptyParameters(): void
    {
        $smarty = null;

        self::assertEquals(0, \smarty_function_rand([], $smarty));
    }

    /**
     * Tests rand function with different min and max values.
     *
     * @param $min
     * @param $max
     *
     *
     * @dataProvider differentMinMaxProvider
     */
    public function testDifferentMinMaxParams($min, $max): void
    {
        $smarty = null;

        $params = [
            'min' => $min,
            'max' => $max,
        ];

        $rand = \smarty_function_rand($params, $smarty);

        self::assertGreaterThanOrEqual($min, $rand);
        self::assertLessThanOrEqual($max, $rand);
    }

    /**
     * Provides data for different min max test.
     *
     * @return array
     */
    public function differentMinMaxProvider()
    {
        return [
            [1, 10],
            [50, 100],
            [20, 30],
            [1, 1],
        ];
    }
}
