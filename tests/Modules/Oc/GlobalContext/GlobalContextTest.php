<?php

namespace OcTest\Modules\Oc\GlobalContext;

use Oc\GlobalContext\GlobalContext;
use OcTest\Modules\TestCase;

/**
 * Class GlobalContextTest
 *
 * @package OcTest\Modules\Oc\GlobalContext
 */
class GlobalContextTest extends TestCase
{
    /**
     * Test all getters of global context.
     */
    public function testGlobalContextGetter()
    {
        $language = 'de';

        $globalContext = new GlobalContext($language);

        self::assertSame($language, $globalContext->getLocale());
    }
}
