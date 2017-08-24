<?php

namespace OcTest\Modules;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class TestCase
 *
 * @package OcTest\Modules\Oc
 */
class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Creates a mock with best practice settings.
     *
     * @param string $originalClassName
     *
     * @return MockObject
     */
    public function createMock($originalClassName)
    {
        return $this->getMockBuilder($originalClassName)
             ->disableOriginalConstructor()
             ->disableOriginalClone()
             ->disableArgumentCloning()
             ->getMock();
    }
}
