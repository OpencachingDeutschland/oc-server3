<?php

namespace OcTest\Modules\Oc\Util;

use Oc\Util\CBench;
use OcTest\Modules\AbstractModuleTest;

class CBenchTest extends AbstractModuleTest
{
    /**
     * @var CBench
     */
    private $cBench;

    public function setUp()
    {
        $this->cBench = new CBench();
    }

    /**
     * @group unit-tests
     */
    public function testStartMethod()
    {
        self::assertEquals(0, $this->cBench->start);

        $this->cBench->start();

        self::assertInternalType('float', $this->cBench->start);
    }

    /**
     * @group unit-tests
     */
    public function testStopMethod()
    {
        self::assertEquals(0, $this->cBench->stop);

        $this->cBench->start();
        $this->cBench->stop();

        self::assertInternalType('float', $this->cBench->stop);
        self::assertGreaterThan(0, $this->cBench->diff());
        self::assertGreaterThan(0, $this->cBench->runTime());
    }
}
