<?php

namespace OcTest\Modules;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as SymfonyKernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class KernelTestCase
 *
 * @package OcTest\Modules
 */
class KernelTestCase extends SymfonyKernelTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets ups the test with a container.
     *
     * @return void
     */
    public function setUp()
    {
        self::$class = \AppKernel::class;
        self::bootKernel();

        $this->container = self::$kernel->getContainer();

        if ($this->container === null) {
            self::markTestSkipped('Container is not initialized');
        }
    }

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
