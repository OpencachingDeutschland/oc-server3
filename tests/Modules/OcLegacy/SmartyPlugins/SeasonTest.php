<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.season.php';

class SeasonTest extends AbstractModuleTest
{
    public function testSeason(): void
    {
        $smarty = null;
        $params['winter'] = 'winter';
        $params['spring'] = 'spring';
        $params['summer'] = 'summer';
        $params['autumn'] = 'autumn';

        $params['season'] = 1;
        self::assertEquals('winter', \smarty_function_season($params, $smarty));

        $params['season'] = 355;
        self::assertEquals('winter', \smarty_function_season($params, $smarty));

        $params['season'] = 356;
        self::assertEquals('winter', \smarty_function_season($params, $smarty));

        $params['season'] = 82;
        self::assertEquals('spring', \smarty_function_season($params, $smarty));

        $params['season'] = 174;
        self::assertEquals('summer', \smarty_function_season($params, $smarty));

        $params['season'] = 265;
        self::assertEquals('autumn', \smarty_function_season($params, $smarty));
    }
}
