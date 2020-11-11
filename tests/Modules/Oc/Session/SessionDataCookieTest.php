<?php

namespace OcTest\Modules\Oc\Session;

use Oc\Session\SessionDataCookie;
use OcTest\Modules\AbstractModuleTest;

class SessionDataCookieTest extends AbstractModuleTest
{
    /**
     * @var SessionDataCookie
     */
    private $sessionDataCookie;

    public function setUp(): void
    {
        global $opt;
        $opt['session']['cookiename'] = 'unit-test';
        $this->sessionDataCookie = new SessionDataCookie();
    }

    /**
     * @group unit-tests
     */
    public function testSessionDataCookieConstructor(): void
    {
        $this->sessionDataCookie->set('testKey', 'testValue');
        self::assertEquals('testValue', $this->sessionDataCookie->get('testKey'));
        self::assertTrue($this->sessionDataCookie->is_set('testKey'));

        $this->sessionDataCookie->un_set('testKey');
        self::assertFalse($this->sessionDataCookie->is_set('testKey'));

        $this->sessionDataCookie->set('testKey', 'testValue');
        $this->sessionDataCookie->set('testKey', 'testValueDefault', 'testValueDefault');
        self::assertEmpty($this->sessionDataCookie->get('testKey'));
    }
}
