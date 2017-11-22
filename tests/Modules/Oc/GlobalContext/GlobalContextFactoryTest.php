<?php

namespace OcTest\Modules\Oc\GlobalContext;

use Oc\GlobalContext\GlobalContext;
use Oc\GlobalContext\GlobalContextFactory;
use Oc\GlobalContext\Provider\LanguageProvider;
use OcTest\Modules\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GlobalContextFactoryTest
 */
class GlobalContextFactoryTest extends TestCase
{
    /**
     * Tests that createFromRequest returns the expected result.
     */
    public function testThatCreateFromRequestReturnsExpectedResult()
    {
        $preferredLanguage = 'de';

        $requestMock = $this->createMock(Request::class);

        $languageProviderMock = $this->createMock(LanguageProvider::class);
        $languageProviderMock->expects(self::once())
            ->method('getPreferredLanguage')
            ->with($requestMock)
            ->willReturn($preferredLanguage);


        $globalContextFactory = new GlobalContextFactory($languageProviderMock);
        $globalContext = $globalContextFactory->createFromRequest($requestMock);

        self::assertInstanceOf(GlobalContext::class, $globalContext);
        self::assertSame($preferredLanguage, $globalContext->getLocale());
    }
}
