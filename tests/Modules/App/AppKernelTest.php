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

    private function getKernel()
    {
        if ($this->kernel) {
            return $this->kernel;
        }
        $kernel = new AppKernel($this->env, true);
        $kernel->boot();

        return $this->kernel = $kernel;
    }

    public function test_if_kernel_boots(): void
    {
        $kernel = $this->getKernel();
        self::assertInstanceOf(AppKernel::class, $kernel::getInstance());
    }

    public function test_registerBundles_returns_array(): void
    {
        self::assertInternalType('array', $this->getKernel()->registerBundles());

        self::assertCount(13, $this->getKernel()->registerBundles());
    }

    public function test_getCacheDir_returns_environment_suffixed_directory(): void
    {
        self::assertContains($this->env, $this->getKernel()->getCacheDir());
    }

    public function test_getLogDir_returns_environment_suffixed_directory(): void
    {
        self::assertContains($this->env, $this->getKernel()->getLogDir());
    }

    public function test_registerContainerFonciguration_loads_environment_suffixed_config_file(): void
    {
        $loaderDummy = new ConfigLoaderDummy();
        $this->getKernel()->registerContainerConfiguration($loaderDummy);

        self::assertContains($this->env, $loaderDummy->getLoadedResource());
    }
}
