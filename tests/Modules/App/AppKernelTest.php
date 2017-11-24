<?php

namespace OcTest\Modules\Oc\Account\Subscriber;

use AppKernel;
use OcTest\Modules\TestCase;
use OcTest\Utils\ConfigLoaderDummy;

class AppKernelTest extends TestCase
{
    /**
     * @var string
     */
    private $env = 'test';

    /**
     * @var AppKernel
     */
    private $kernel;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $kernel = new AppKernel($this->env, true);
        $kernel->boot();

        $this->kernel = $kernel;
    }

    public function test_if_kernel_boots()
    {
        $kernel = $this->kernel;
        self::assertInstanceOf(AppKernel::class, $kernel::getInstance());
    }

    public function test_registerBundles_returns_array()
    {
        self::assertInternalType('array', $this->kernel->registerBundles());

        self::assertCount(16, $this->kernel->registerBundles());
    }

    public function test_getCacheDir_returns_environment_suffixed_directory()
    {
        self::assertContains($this->env, $this->kernel->getCacheDir());
    }

    public function test_getLogDir_returns_environment_suffixed_directory()
    {
        self::assertContains($this->env, $this->kernel->getLogDir());
    }

    public function test_registerContainerFonciguration_loads_environment_suffixed_config_file()
    {
        $loaderDummy = new ConfigLoaderDummy();
        $this->kernel->registerContainerConfiguration($loaderDummy);

        self::assertContains($this->env, $loaderDummy->getLoadedResource());
    }
}
