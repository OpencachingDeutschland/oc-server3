<?php

namespace OcTest\Modules;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Class TestCase
 *
 * @package OcTest\Modules\Oc
 */
class TestCase extends PHPUnitTestCase
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
