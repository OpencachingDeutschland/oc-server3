<?php

namespace OcTest\Modules\Oc\GlobalContext;

use Oc\GlobalContext\GlobalContext;
use OcTest\Modules\TestCase;

/**
 * Class GlobalContextTest
 */
class GlobalContextTest extends TestCase
{
    /**
     * Test all getters of global context.
     */
    public function testGlobalContextGetter()
    {
        $language = 'de';
        $defaultLocale = 'en';

        $globalContext = new GlobalContext($defaultLocale, $language);

        self::assertSame($defaultLocale, $globalContext->getDefaultLocale());
        self::assertSame($language, $globalContext->getLocale());
    }
}
