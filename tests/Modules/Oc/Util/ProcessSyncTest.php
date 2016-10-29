<?php

namespace OcTest\Modules\Oc\Util;

use Oc\Util\ProcessSync;
use OcTest\Modules\AbstractModuleTest;

class ProcessSyncTest extends AbstractModuleTest
{
    /**
     * @var string
     */
    private $name = 'test';

    /**
     * @var ProcessSync
     */
    private $processSync;

    public function setUp()
    {
        $this->processSync = new ProcessSync($this->name);
    }

    public function testEnterMethod()
    {
        self::assertTrue($this->processSync->enter());
        self::assertFalse($this->processSync->enter());
        self::assertTrue($this->processSync->leave());
    }

    public function testLeaveMethod()
    {
        self::assertFalse($this->processSync->leave('test message'));
    }

    public function testCheckDaemonMethod()
    {
        $file = fopen(__DIR__ . '/../../../../htdocs/cache2/' . $this->name . '.pid', 'w');
        fwrite($file, 'pid file', 100);
        fclose($file);

        self::assertFalse($this->processSync->enter());

        $file = fopen(__DIR__ . '/../../../../htdocs/cache2/' . $this->name . '.pid', 'w');
        fwrite($file, '10000', 100);
        fclose($file);

        self::assertFalse($this->processSync->enter());


    }
}
